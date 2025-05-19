<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Entrega;
use App\Models\Curso; // Para obtener info del curso y sus módulos/tareas
use App\Models\Modulo;
use App\Models\Tarea;
use Illuminate\View\View;
use Illuminate\Support\Collection;

class CalificacionController extends Controller
{
    /**
     * Muestra un resumen de las calificaciones del estudiante,
     * con promedios por curso y por módulo.
     */
    public function index(): View
    {
        $estudiante = Auth::user();

        // Obtener todas las entregas del estudiante que ya han sido calificadas
        $entregasCalificadas = $estudiante->entregasRealizadas()
                                        ->whereNotNull('calificacion')
                                        ->with(['tarea.curso.modulos', 'tarea.modulo']) // Cargar relaciones
                                        ->orderBy('fecha_calificacion', 'desc')
                                        ->get();

        // Agrupar entregas por curso_id para facilitar el procesamiento
        $entregasPorCursoId = $entregasCalificadas->groupBy('tarea.curso_id');

        $datosParaVista = collect(); // Aquí guardaremos los datos procesados por curso

        foreach ($entregasPorCursoId as $cursoId => $entregasDelCurso) {
            if (empty($cursoId) || !$entregasDelCurso->first()) continue; // Saltar si no hay curso_id o entregas

            $curso = $entregasDelCurso->first()->tarea->curso; // Obtener el objeto Curso
            if (!$curso) continue; // Saltar si no se puede obtener el curso

            $tareasDelCurso = $curso->tareas()->get()->keyBy('id'); // Todas las tareas del curso, indexadas
            $modulosDelCurso = $curso->modulos()->orderBy('orden')->get();

            $totalPuntosObtenidosCurso = 0;
            $totalPuntosPosiblesCurso = 0;
            $promediosPorModulo = collect();

            // Calcular promedio por cada módulo del curso
            foreach ($modulosDelCurso as $modulo) {
                $tareasDelModulo = $tareasDelCurso->where('modulo_id', $modulo->id);
                $puntosObtenidosModulo = 0;
                $puntosPosiblesModulo = 0;

                foreach ($tareasDelModulo as $tarea) {
                    if ($tarea->puntos_maximos !== null && $tarea->puntos_maximos > 0) {
                        $entrega = $entregasDelCurso->where('tarea_id', $tarea->id)->first();
                        if ($entrega) { // Si hay entrega calificada para esta tarea
                            $puntosObtenidosModulo += $entrega->calificacion;
                            $puntosPosiblesModulo += $tarea->puntos_maximos;
                        } else {
                            // Opción: Sumar puntos posibles si la tarea es puntuable pero no entregada/calificada
                            // $puntosPosiblesModulo += $tarea->puntos_maximos;
                        }
                    }
                }
                $promedioModulo = ($puntosPosiblesModulo > 0)
                                 ? round(($puntosObtenidosModulo / $puntosPosiblesModulo) * 100, 2)
                                 : null;
                $promediosPorModulo->put($modulo->id, [
                    'titulo' => $modulo->titulo,
                    'promedio' => $promedioModulo,
                    'obtenidos' => $puntosObtenidosModulo,
                    'posibles' => $puntosPosiblesModulo,
                ]);
                $totalPuntosObtenidosCurso += $puntosObtenidosModulo;
                $totalPuntosPosiblesCurso += $puntosPosiblesModulo;
            }

            // Calcular promedio de tareas SIN módulo asignado en este curso
            $tareasSinModulo = $tareasDelCurso->whereNull('modulo_id');
            $puntosObtenidosSinModulo = 0;
            $puntosPosiblesSinModulo = 0;
            foreach ($tareasSinModulo as $tarea) {
                 if ($tarea->puntos_maximos !== null && $tarea->puntos_maximos > 0) {
                     $entrega = $entregasDelCurso->where('tarea_id', $tarea->id)->first();
                     if ($entrega) {
                         $puntosObtenidosSinModulo += $entrega->calificacion;
                         $puntosPosiblesSinModulo += $tarea->puntos_maximos;
                     } // else { $puntosPosiblesSinModulo += $tarea->puntos_maximos; }
                 }
            }
            $promedioSinModulo = ($puntosPosiblesSinModulo > 0)
                               ? round(($puntosObtenidosSinModulo / $puntosPosiblesSinModulo) * 100, 2)
                               : null;
            $totalPuntosObtenidosCurso += $puntosObtenidosSinModulo;
            $totalPuntosPosiblesCurso += $puntosPosiblesSinModulo;

            // Calcular promedio general del curso
            $promedioGeneralCurso = ($totalPuntosPosiblesCurso > 0)
                              ? round(($totalPuntosObtenidosCurso / $totalPuntosPosiblesCurso) * 100, 2)
                              : null;

            $datosParaVista->put($curso->id, [
                'curso' => $curso,
                'entregas' => $entregasDelCurso->sortBy('tarea.titulo'), // Entregas individuales para este curso
                'promediosPorModulo' => $promediosPorModulo,
                'promedioSinModulo' => $promedioSinModulo, // Promedio de tareas sin módulo para este curso
                'promedioGeneralCurso' => $promedioGeneralCurso,
                'puntosObtenidosCurso' => $totalPuntosObtenidosCurso,
                'puntosPosiblesCurso' => $totalPuntosPosiblesCurso,
            ]);
        }

        return view('alumno.calificaciones.index', compact('datosParaVista'));
    }
}
