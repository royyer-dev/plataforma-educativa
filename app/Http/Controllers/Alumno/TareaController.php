<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use App\Models\Tarea;
use App\Models\Curso; // Necesario para recibir el curso padre
use App\Models\Inscripcion; // Para verificar inscripción
use App\Models\Entrega; // Para ver y crear entregas
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Para obtener el estudiante
use Illuminate\Support\Facades\Storage; // Para manejo de archivos en entregas
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon; // Para manejo de fechas
// Asegúrate de importar la notificación si aún no lo has hecho
use App\Notifications\NuevaEntregaNotification;

class TareaController extends Controller
{
    /**
     * Muestra los detalles de una tarea específica para el estudiante.
     * También busca si ya existe una entrega previa.
     * Ruta: GET /alumno/cursos/{curso}/tareas/{tarea}
     * Nombre: alumno.cursos.tareas.show
     */
    public function show(Curso $curso, Tarea $tarea): View | RedirectResponse
    {
        $estudiante = Auth::user();

        // 1. Verificar que la tarea pertenezca al curso especificado
        if ($tarea->curso_id !== $curso->id) {
            abort(404, 'Tarea no encontrada en este curso.');
        }

        // 2. Verificar que el estudiante esté inscrito y activo en el curso
        $inscripcion = $estudiante->inscripciones()
                                  ->where('curso_id', $curso->id)
                                  ->where('estado', 'activo')
                                  ->first();

        if (!$inscripcion) {
             return redirect()->route('alumno.cursos.index')
                              ->with('error', 'No estás inscrito en este curso o tu inscripción no está activa.');
        }

        // 3. Buscar la última entrega del estudiante para esta tarea
        $entregaEstudiante = $estudiante->entregasRealizadas()
                                       ->where('tarea_id', $tarea->id)
                                       ->orderBy('created_at', 'desc') // Obtener la última si hay reintentos
                                       ->first();

        // 4. Determinar si el estudiante puede entregar (o volver a entregar)
        $puedeEntregar = $this->checkIfStudentCanSubmit($tarea, $entregaEstudiante);

        // 5. Pasar los datos a la vista
        return view('alumno.tareas.show', compact('curso', 'tarea', 'entregaEstudiante', 'puedeEntregar'));
    }

    /**
     * Almacena la entrega de una tarea realizada por el estudiante.
     * Ruta: POST /alumno/cursos/{curso}/tareas/{tarea}/entregar
     * Nombre: alumno.cursos.tareas.storeEntrega
     */
    public function storeEntrega(Request $request, Curso $curso, Tarea $tarea): RedirectResponse
    {
        $estudiante = Auth::user();

        // --- Verificaciones Previas ---
        // 1. Verificar que la tarea pertenezca al curso
        if ($tarea->curso_id !== $curso->id) {
            abort(404);
        }
        // 2. Verificar que el estudiante esté inscrito y activo
        if (!$estudiante->inscripciones()->where('curso_id', $curso->id)->where('estado', 'activo')->exists()) {
            return back()->with('error', 'No puedes realizar entregas en este curso.'); // Redirige atrás
        }
        // 3. Verificar si ya existe una entrega y si se permiten reintentos (lógica simple por ahora)
        $entregaExistente = $estudiante->entregasRealizadas()->where('tarea_id', $tarea->id)->first();
        // (Aquí podrías añadir lógica para re-entregas si fuera necesario)

        // 4. Verificar si la fecha límite ha pasado (considerando entregas tardías)
        if (!$this->checkIfStudentCanSubmit($tarea, $entregaExistente)) {
             return back()->with('error', 'El plazo para entregar esta tarea ha finalizado.');
        }


        // --- Validación de la Entrega (depende del tipo de tarea) ---
        $rules = [];
        $tipoEntrega = $tarea->tipo_entrega;

        if ($tipoEntrega === 'archivo') {
            $rules['archivo_entrega'] = 'required|file|mimes:pdf,doc,docx,zip,rar,jpg,png|max:10240'; // Max 10MB
        } elseif ($tipoEntrega === 'texto') {
            $rules['texto_entrega'] = 'required|string|max:65000';
        } elseif ($tipoEntrega === 'url') {
            $rules['url_entrega'] = 'required|url|max:2048';
        }

        $validatedData = $request->validate($rules);


        // --- Procesar y Guardar la Entrega ---
        $datosEntrega = [
            'tarea_id' => $tarea->id,
            'estudiante_id' => $estudiante->id,
            'estado_entrega' => 'entregado', // Estado inicial
        ];

        // Determinar si es entrega tardía
        if ($tarea->fecha_limite && Carbon::now()->gt($tarea->fecha_limite)) {
             $datosEntrega['estado_entrega'] = 'entregado_tarde';
        }

        // Guardar el contenido específico
        if ($tipoEntrega === 'archivo' && $request->hasFile('archivo_entrega')) {
             $path = $request->file('archivo_entrega')->store("entregas/{$curso->id}/{$tarea->id}", 'public');
             $datosEntrega['ruta_archivo'] = $path;
        } elseif ($tipoEntrega === 'texto') {
            $datosEntrega['texto_entrega'] = $validatedData['texto_entrega'];
        } elseif ($tipoEntrega === 'url') {
            $datosEntrega['url_entrega'] = $validatedData['url_entrega'];
        }

        // Crear o actualizar la entrega
        $entrega = Entrega::updateOrCreate( // Guardar la entrega creada/actualizada
            ['tarea_id' => $tarea->id, 'estudiante_id' => $estudiante->id],
            $datosEntrega
        );

        // --- vvv Notificar a los Profesores del Curso vvv ---
        // Cargar relaciones necesarias si no están ya
        $entrega->load(['estudiante', 'tarea.curso.profesores']);
        $profesores = $entrega->tarea->curso->profesores;

        if ($profesores->isNotEmpty()) {
            foreach ($profesores as $profesor) {
                if ($profesor instanceof \App\Models\Usuario) {
                     $profesor->notify(new NuevaEntregaNotification($entrega));
                }
            }
        }
        // --- ^^^ Fin Notificar Profesores ^^^ ---


        // Redirigir de vuelta a la página de la tarea con mensaje de éxito
        return redirect()->route('alumno.cursos.tareas.show', [$curso->id, $tarea->id])
                         ->with('status', '¡Tarea entregada exitosamente!');
    }


    /**
     * Función auxiliar para determinar si el estudiante puede entregar la tarea.
     */
    private function checkIfStudentCanSubmit(Tarea $tarea, ?Entrega $entregaExistente): bool
    {
        // (Aquí podrías añadir lógica más compleja para re-entregas)
        // if ($entregaExistente && !$permitirReentrega) { return false; }

        $now = Carbon::now();
        $fechaLimite = $tarea->fecha_limite;
        $fechaLimiteTardia = $tarea->fecha_limite_tardia;
        $permiteTardia = $tarea->permite_entrega_tardia;

        if (!$fechaLimite) return true; // Sin límite
        if ($now->lte($fechaLimite)) return true; // Dentro del plazo normal
        if ($now->gt($fechaLimite) && $permiteTardia && (!$fechaLimiteTardia || $now->lte($fechaLimiteTardia))) return true; // Dentro del plazo tardío

        return false; // Plazo vencido
    }

}
