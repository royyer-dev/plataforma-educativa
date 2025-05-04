<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\Tarea;
use App\Models\Curso;
use App\Models\Modulo;
use App\Models\Entrega;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;
use App\Notifications\NuevaTareaNotification; // <-- Importar la notificación
use App\Notifications\TareaCalificadaNotification;

class TareaController extends Controller
{
    /**
     * Muestra el formulario para crear una nueva tarea.
     */
    public function create(Curso $curso): View
    {
        $this->authorizeTeacherAccess($curso);
        $modulos = $curso->modulos()->orderBy('orden')->pluck('titulo', 'id');
        $tipos_entrega = $this->getTiposEntrega();
        return view('docente.tareas.create', compact('curso', 'modulos', 'tipos_entrega'));
    }

    /**
     * Almacena una nueva tarea.
     */
    public function store(Request $request, Curso $curso): RedirectResponse
    {
        $this->authorizeTeacherAccess($curso);
        $validatedData = $this->validateTarea($request, $curso); // Usar método auxiliar
        $validatedData['curso_id'] = $curso->id;
        $validatedData['creado_por'] = Auth::id();
        $validatedData['modulo_id'] = $request->input('modulo_id') ?: null;
        $validatedData['permite_entrega_tardia'] = $request->boolean('permite_entrega_tardia');
        if (!$validatedData['permite_entrega_tardia']) {
            $validatedData['fecha_limite_tardia'] = null;
        }

        // Crear la tarea asociada al curso
        $tarea = $curso->tareas()->create($validatedData); // Guardar la tarea creada

        // --- vvv Notificar a los Estudiantes Inscritos y Activos vvv ---
        // Obtener estudiantes con inscripción activa en este curso
        $estudiantesActivos = $curso->estudiantes() // Asume relación 'estudiantes' en modelo Curso
                                   ->wherePivot('estado', 'activo') // Filtrar por estado en tabla pivote 'inscripciones'
                                   ->get();

        if ($estudiantesActivos->isNotEmpty()) {
            foreach ($estudiantesActivos as $estudiante) {
                // Verificar que sea una instancia de Usuario antes de notificar
                if ($estudiante instanceof \App\Models\Usuario) {
                    $estudiante->notify(new NuevaTareaNotification($tarea));
                }
            }
        }
        // --- ^^^ Fin Notificar Estudiantes ^^^ ---

        // Redirigir de vuelta a la vista del curso con mensaje de éxito
        return redirect()->route('docente.cursos.show', $curso->id)
                         ->with('status', '¡Tarea creada exitosamente!');
    }

     /**
     * Muestra el formulario para editar una tarea existente.
     */
    public function edit(Curso $curso, Tarea $tarea): View
    {
        $this->authorizeTeacherAccess($curso);
        $this->ensureTareaBelongsToCourse($curso, $tarea);
        $modulos = $curso->modulos()->orderBy('orden')->pluck('titulo', 'id');
        $tipos_entrega = $this->getTiposEntrega();
        return view('docente.tareas.edit', compact('curso', 'tarea', 'modulos', 'tipos_entrega'));
    }

    /**
     * Actualiza una tarea existente.
     */
    public function update(Request $request, Curso $curso, Tarea $tarea): RedirectResponse
    {
        $this->authorizeTeacherAccess($curso);
        $this->ensureTareaBelongsToCourse($curso, $tarea);
        $validatedData = $this->validateTarea($request, $curso, $tarea); // Pasar $tarea para validación unique si fuera necesario
        $validatedData['permite_entrega_tardia'] = $request->boolean('permite_entrega_tardia');
        if (!$validatedData['permite_entrega_tardia']) {
            $validatedData['fecha_limite_tardia'] = null;
        }
        $tarea->update($validatedData);
        // Podrías notificar a los estudiantes sobre la actualización si lo deseas
        // foreach($curso->estudiantes()->wherePivot('estado', 'activo')->get() as $estudiante) { ... }
        return redirect()->route('docente.cursos.show', $curso->id)
                         ->with('status', '¡Tarea actualizada exitosamente!');
    }

    /**
     * Elimina una tarea específica.
     */
    public function destroy(Curso $curso, Tarea $tarea): RedirectResponse
    {
        $this->authorizeTeacherAccess($curso);
        $this->ensureTareaBelongsToCourse($curso, $tarea);
        // Considerar borrar entregas asociadas si no hay cascade
        $tarea->delete();
        return redirect()->route('docente.cursos.show', $curso->id)
                         ->with('status', '¡Tarea eliminada exitosamente!');
    }


    // --- Métodos para Entregas ---

    /**
     * Muestra la lista de entregas realizadas por los estudiantes para una tarea específica.
     */
    public function verEntregas(Curso $curso, Tarea $tarea): View
    {
        $this->authorizeTeacherAccess($curso);
        $this->ensureTareaBelongsToCourse($curso, $tarea);

        $entregas = $tarea->entregas()
                          ->with('estudiante') // Carga la relación estudiante
                          ->orderBy('created_at', 'desc')
                          ->paginate(20);

        return view('docente.tareas.entregas.index', compact('curso', 'tarea', 'entregas'));
    }

    /**
     * Muestra el formulario/interfaz para calificar una entrega específica.
     */
    public function mostrarFormularioCalificar(Curso $curso, Tarea $tarea, Entrega $entrega): View
    {
        // Verificar permisos y pertenencia
        $this->authorizeTeacherAccess($curso);
        $this->ensureTareaBelongsToCourse($curso, $tarea);
        $this->ensureEntregaBelongsToTarea($tarea, $entrega);

        // Cargar relación estudiante por si no viene por defecto
        $entrega->load('estudiante');

        // Pasar datos a la vista de calificación
        return view('docente.tareas.entregas.calificar', compact('curso', 'tarea', 'entrega'));
    }

    /**
     * Guarda la calificación y retroalimentación para una entrega.
     */
    public function guardarCalificacion(Request $request, Curso $curso, Tarea $tarea, Entrega $entrega): RedirectResponse
    {
         // Verificar permisos y pertenencia
        $this->authorizeTeacherAccess($curso);
        $this->ensureTareaBelongsToCourse($curso, $tarea);
        $this->ensureEntregaBelongsToTarea($tarea, $entrega);

        // Validación de los datos de calificación
        $maxPuntos = $tarea->puntos_maximos ? "|max:{$tarea->puntos_maximos}" : '';
        $validatedData = $request->validate([
            'calificacion' => "required|numeric|min:0{$maxPuntos}",
            'retroalimentacion' => 'nullable|string|max:5000',
        ]);

        // Actualizar la entrega
        $entrega->update([
            'calificacion' => $validatedData['calificacion'],
            'retroalimentacion' => $validatedData['retroalimentacion'],
            'calificado_por' => Auth::id(),
            'fecha_calificacion' => Carbon::now(),
            'estado_entrega' => 'calificado'
        ]);

        // Aquí podrías añadir la notificación al estudiante sobre la calificación
        // if ($entrega->estudiante) {
        //     $entrega->estudiante->notify(new TareaCalificadaNotification($entrega));
        // }

        // Redirigir de vuelta a la lista de entregas
        return redirect()->route('docente.cursos.tareas.entregas.index', [$curso->id, $tarea->id])
                         ->with('status', '¡Entrega calificada exitosamente!');
    }


    // --- Métodos Auxiliares ---

    /**
     * Verifica si el usuario autenticado es profesor de este curso.
     */
    protected function authorizeTeacherAccess(Curso $curso): void
    {
        if (!$curso->profesores()->where('profesor_id', Auth::id())->exists()) {
            abort(403, 'No tienes permiso para gestionar este curso.');
        }
    }

     /**
     * Verifica si una tarea pertenece realmente al curso especificado.
     */
    protected function ensureTareaBelongsToCourse(Curso $curso, Tarea $tarea): void
    {
        if ($tarea->curso_id !== $curso->id) {
            abort(404, 'Tarea no encontrada en este curso.');
        }
    }

    /**
     * Verifica si una entrega pertenece realmente a la tarea especificada.
     */
    protected function ensureEntregaBelongsToTarea(Tarea $tarea, Entrega $entrega): void
    {
        if ($entrega->tarea_id !== $tarea->id) {
            abort(404, 'Entrega no encontrada para esta tarea.');
        }
    }

    /**
     * Retorna los tipos de entrega posibles (para formularios).
     */
    private function getTiposEntrega(): array
    {
         return [
            'archivo' => 'Subir Archivo',
            'texto' => 'Texto en Línea',
            'url' => 'Enviar Enlace (URL)',
            'ninguno' => 'Sin Entrega Online (Offline)',
        ];
    }

    /**
     * Valida los datos comunes para crear/actualizar una tarea.
     * Se añade el parámetro opcional $tarea para ignorar su propio ID en validaciones 'unique' si fuera necesario.
     */
    private function validateTarea(Request $request, Curso $curso, ?Tarea $tarea = null): array
    {
         // Aquí podrías añadir reglas 'unique' si algún campo de tarea debe ser único dentro del curso,
         // ignorando el $tarea->id si se está actualizando. Ejemplo:
         // $tituloRule = Rule::unique('tareas', 'titulo')->where('curso_id', $curso->id);
         // if ($tarea) { $tituloRule->ignore($tarea->id); }

         return $request->validate([
            'titulo' => 'required|string|max:255', // Podrías añadir $tituloRule aquí
            'descripcion' => 'nullable|string',
            'modulo_id' => 'nullable|integer|exists:modulos,id,curso_id,' . $curso->id,
            'tipo_entrega' => 'required|in:archivo,texto,url,ninguno',
            'puntos_maximos' => 'nullable|numeric|min:0',
            'fecha_limite' => 'nullable|date',
            'fecha_limite_tardia' => 'nullable|date|after_or_equal:fecha_limite',
        ]);
    }
    
}
