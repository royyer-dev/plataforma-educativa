<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\Tarea;
use App\Models\Entrega;
use App\Models\Curso; // Necesario para la relación con Tarea
use Carbon\Carbon;     // Para el manejo de fechas

class AgendaController extends Controller
{
    /**
     * Muestra la agenda de tareas del estudiante: próximas a vencer,
     * vencidas sin entregar y entregadas recientemente.
     * Ruta: GET /alumno/agenda-tareas
     * Nombre: alumno.agenda.index
     */
    public function index(): View
    {
        $estudiante = Auth::user();
        $now = Carbon::now();

        // IDs de los cursos donde el estudiante está activo
        $cursosActivosIds = $estudiante->inscripciones()
                                     ->where('estado', 'activo')
                                     ->pluck('curso_id');

        $proximasTareas = collect();
        $vencidasSinEntregar = collect();
        $entregadasRecientemente = collect();

        if ($cursosActivosIds->isNotEmpty()) {
            // Query base para las tareas visibles del estudiante en sus cursos activos
            $queryTareasVisibles = Tarea::whereIn('curso_id', $cursosActivosIds)
                                        // ->where('tareas.estado', 'publicada') // Descomentar si las tareas tienen un campo 'estado' para visibilidad
                                        ->with('curso'); // Cargar la relación curso para cada tarea

            // 1. Tareas Próximas a Vencer (que no han sido entregadas por el estudiante)
            $proximasTareas = (clone $queryTareasVisibles)
                ->whereNotNull('fecha_limite')
                ->where('fecha_limite', '>=', $now)
                ->whereDoesntHave('entregas', function ($query) use ($estudiante) {
                    $query->where('estudiante_id', $estudiante->id);
                })
                ->orderBy('fecha_limite', 'asc')
                ->limit(10) // Puedes ajustar el límite
                ->get();

            // 2. Tareas Vencidas SIN Entregar por el estudiante
            $vencidasSinEntregar = (clone $queryTareasVisibles)
                ->whereNotNull('fecha_limite')
                ->where(function ($query) use ($now) {
                    $query->where('fecha_limite', '<', $now) // Fecha límite normal ya pasó
                          ->orWhere(function ($subQuery) use ($now) { // O permite tardía y fecha tardía ya pasó
                              $subQuery->where('permite_entrega_tardia', true)
                                       ->whereNotNull('fecha_limite_tardia')
                                       ->where('fecha_limite_tardia', '<', $now);
                          });
                })
                ->whereDoesntHave('entregas', function ($query) use ($estudiante) {
                    $query->where('estudiante_id', $estudiante->id);
                })
                ->orderBy('fecha_limite', 'desc') // Las más recientemente vencidas primero
                ->limit(10) // Puedes ajustar el límite
                ->get();

            // 3. Tareas Entregadas Recientemente por el estudiante
            $entregadasRecientemente = $estudiante->entregasRealizadas()
                ->with(['tarea.curso']) // Cargar tarea y curso de la tarea
                ->orderBy('updated_at', 'desc') // Ordenar por la fecha de la última actualización de la entrega
                ->limit(5) // Puedes ajustar el límite
                ->get();
        }

        return view('alumno.agenda.index', compact(
            'proximasTareas',
            'vencidasSinEntregar',
            'entregadasRecientemente'
        ));
    }
}
