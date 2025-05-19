<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use App\Models\Tarea;
use App\Models\Curso; // Necesario para recibir el curso padre
use App\Models\Inscripcion; // Para verificar inscripción
use App\Models\Entrega; // Para ver y crear entregas
use App\Models\Usuario; // Asegurarse que Usuario esté importado para type hinting si es necesario
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Para obtener el estudiante
use Illuminate\Support\Facades\Storage; // Para manejo de archivos en entregas
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon; // Para manejo de fechas
use App\Notifications\NuevaEntregaNotification; // Notificación para el docente


class DashboardController extends Controller
{
    public function index(): View
    {
        $estudiante = Auth::user();

        // 1. Obtener Cursos Activos
        $cursosActivos = $estudiante->cursosInscritos() // Usa la relación definida en Usuario
                                  ->wherePivot('estado', 'activo') // Filtra por estado activo en la tabla inscripciones
                                  ->with('profesores') // Carga profesores opcionalmente
                                  ->orderBy('titulo')
                                  ->limit(6) // Limitar para no saturar el dashboard
                                  ->get();

        // 2. Obtener Próximas Tareas (de cursos activos del estudiante)
        $cursosActivosIds = $cursosActivos->pluck('id');
        $proximasTareas = collect(); // Inicializar como colección vacía

        if ($cursosActivosIds->isNotEmpty()) {
            $proximasTareasQuery = Tarea::whereIn('curso_id', $cursosActivosIds)
                                ->whereNotNull('fecha_limite') // Solo tareas con fecha límite definida
                                ->where('fecha_limite', '>=', Carbon::now()) // Fecha límite hoy o futura
                                ->with('curso'); // Cargar curso para mostrar nombre

            // --- IMPORTANTE: AJUSTA ESTA SECCIÓN SEGÚN TU TABLA 'tareas' ---
            // Si tu tabla 'tareas' tiene una columna 'estado' para controlar visibilidad (ej: 'publicada', 'borrador')
            // Descomenta y ajusta la siguiente línea. Si no, puedes eliminarla.
            // Ejemplo: $proximasTareasQuery->where('estado', 'publicada');
            // O si el campo se llama diferente: $proximasTareasQuery->where('status_tarea', 'visible');

            $proximasTareas = $proximasTareasQuery->orderBy('fecha_limite', 'asc') // Las más próximas primero
                                                ->limit(5) // Limitar número de tareas mostradas
                                                ->get();
        }

        // 3. Pasar los datos a la vista
        return view('alumno.dashboard', compact('cursosActivos', 'proximasTareas'));
    }
    /**
     * Muestra los detalles de una tarea específica para el estudiante.
     * Busca si ya existe una entrega previa y determina si se puede entregar.
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
             return redirect()->route('alumno.carreras.index') // Redirigir a la lista de carreras
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
     * Notifica a los profesores del curso sobre la nueva entrega.
     * Ruta: POST /alumno/cursos/{curso}/tareas/{tarea}/entregar
     * Nombre: alumno.cursos.tareas.storeEntrega
     */
    public function storeEntrega(Request $request, Curso $curso, Tarea $tarea): RedirectResponse
    {
        $estudiante = Auth::user();

        // --- Verificaciones Previas ---
        if ($tarea->curso_id !== $curso->id) { abort(404,'Tarea no pertenece al curso.'); }

        if (!$estudiante->inscripciones()->where('curso_id', $curso->id)->where('estado', 'activo')->exists()) {
            return back()->with('error', 'No puedes realizar entregas en este curso.');
        }

        $entregaExistente = $estudiante->entregasRealizadas()->where('tarea_id', $tarea->id)->first();
        // Aquí se podría añadir lógica para permitir múltiples intentos o editar entregas si se desea.
        // Por ahora, si ya existe una, la lógica de checkIfStudentCanSubmit podría impedir una nueva.

        if (!$this->checkIfStudentCanSubmit($tarea, $entregaExistente)) {
             return back()->with('error', 'El plazo para entregar esta tarea ha finalizado o ya has realizado la entrega.');
        }

        // --- Validación de la Entrega (depende del tipo de tarea) ---
        $rules = [];
        $tipoEntrega = $tarea->tipo_entrega;

        if ($tipoEntrega === 'archivo') {
            $rules['archivo_entrega'] = 'required|file|mimes:pdf,doc,docx,zip,rar,jpg,jpeg,png,txt|max:10240'; // Max 10MB
        } elseif ($tipoEntrega === 'texto') {
            $rules['texto_entrega'] = 'required|string|max:65000'; // Límite TEXT de MySQL
        } elseif ($tipoEntrega === 'url') {
            $rules['url_entrega'] = 'required|url|max:2048';
        }
        // Si el tipo es 'ninguno', el formulario en la vista no debería permitir el envío.

        $validatedData = $request->validate($rules);

        // --- Procesar y Guardar la Entrega ---
        $datosEntrega = [
            'tarea_id' => $tarea->id,
            'estudiante_id' => $estudiante->id,
            'estado_entrega' => 'entregado', // Estado inicial
            // 'fecha_entrega' se establece por defecto con timestamps
        ];

        if ($tarea->fecha_limite && Carbon::now()->gt($tarea->fecha_limite)) {
             $datosEntrega['estado_entrega'] = 'entregado_tarde';
        }

        if ($tipoEntrega === 'archivo' && $request->hasFile('archivo_entrega')) {
             $path = $request->file('archivo_entrega')->store("entregas/{$curso->id}/{$tarea->id}", 'public');
             $datosEntrega['ruta_archivo'] = $path;
        } elseif ($tipoEntrega === 'texto') {
            $datosEntrega['texto_entrega'] = $validatedData['texto_entrega'];
        } elseif ($tipoEntrega === 'url') {
            $datosEntrega['url_entrega'] = $validatedData['url_entrega'];
        }

        // Crear o actualizar la entrega (updateOrCreate es útil si permites re-entregas)
        $entrega = Entrega::updateOrCreate(
            ['tarea_id' => $tarea->id, 'estudiante_id' => $estudiante->id], // Claves para buscar
            $datosEntrega // Valores para crear o actualizar
        );

        // Notificar a los Profesores del Curso
        $entrega->load(['estudiante', 'tarea.curso.profesores']); // Cargar relaciones para la notificación
        $profesores = optional(optional($entrega->tarea)->curso)->profesores;

        if ($profesores && $profesores->isNotEmpty()) {
            foreach ($profesores as $profesor) {
                if ($profesor instanceof \App\Models\Usuario) { // Verificar tipo
                     $profesor->notify(new NuevaEntregaNotification($entrega));
                }
            }
        }

        return redirect()->route('alumno.cursos.tareas.show', [$curso->id, $tarea->id])
                         ->with('status', '¡Tarea entregada exitosamente!');
    }


    /**
     * Función auxiliar para determinar si el estudiante puede entregar la tarea,
     * considerando fechas límite y si ya existe una entrega.
     */
    private function checkIfStudentCanSubmit(Tarea $tarea, ?Entrega $entregaExistente): bool
    {
        // Lógica para re-entregas (ejemplo simple: no permitir si ya está calificada)
        // if ($entregaExistente && $entregaExistente->calificacion !== null) {
        //     return false; // No se puede re-entregar si ya está calificada
        // }
        // Podrías añadir una columna 'intentos_permitidos' en Tarea y 'numero_intento' en Entrega
        // para una lógica de reintentos más avanzada.

        $now = Carbon::now();
        $fechaLimite = $tarea->fecha_limite; // Asume que es un objeto Carbon o null
        $fechaLimiteTardia = $tarea->fecha_limite_tardia; // Asume que es un objeto Carbon o null
        $permiteTardia = $tarea->permite_entrega_tardia;

        // Si no hay fecha límite, siempre se puede entregar (a menos que ya haya una y no se permitan reintentos)
        if (!$fechaLimite) {
            return true;
        }

        // Si la fecha actual es ANTES o IGUAL a la fecha límite normal
        if ($now->lte($fechaLimite)) {
            return true;
        }

        // Si la fecha actual es DESPUÉS de la fecha límite normal,
        // pero se permiten entregas tardías Y (no hay fecha límite tardía O la fecha actual es ANTES o IGUAL a la tardía)
        if ($now->gt($fechaLimite) && $permiteTardia) {
            if (!$fechaLimiteTardia || $now->lte($fechaLimiteTardia)) {
                return true;
            }
        }

        // En cualquier otro caso (plazo normal y tardío vencidos), ya no puede entregar
        return false;
    }
}