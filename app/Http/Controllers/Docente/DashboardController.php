<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\Inscripcion;
use App\Models\Curso;
use App\Models\Entrega;
use App\Models\Tarea;
use App\Models\Usuario;

class DashboardController extends Controller
{
    public function index(): View
    {
        $docente = Auth::user();

        // Obtener cursos del docente y contar estudiantes activos en cada uno
        $cursosDocente = $docente->cursosImpartidos()
                                 ->withCount(['estudiantes as estudiantes_activos_count' => function ($query) {
                                     $query->where('inscripciones.estado', 'activo');
                                 }])
                                 ->orderBy('titulo')
                                 ->get();

        $cursosIds = $cursosDocente->pluck('id');

        // Estadísticas generales
        $solicitudesPendientesCount = 0;
        if ($cursosIds->isNotEmpty()) {
             $solicitudesPendientesCount = Inscripcion::where('estado', 'pendiente')
                                                ->whereIn('curso_id', $cursosIds)
                                                ->count();
        }
        // Contamos solo los cursos que el docente imparte y están publicados
        $totalCursosActivos = $docente->cursosImpartidos()->where('estado', 'publicado')->count();
        $totalEstudiantesInscritos = $cursosDocente->sum('estudiantes_activos_count');

        $totalTareasSinCalificar = 0;
        if ($cursosIds->isNotEmpty()) {
            $totalTareasSinCalificar = Entrega::whereHas('tarea', function ($query) use ($cursosIds) {
                                                $query->whereIn('curso_id', $cursosIds);
                                            })
                                            ->whereNull('calificacion')
                                            ->count();
        }

        // Actividad Reciente
        $ultimasEntregas = collect();
        if ($cursosIds->isNotEmpty()) {
            $ultimasEntregas = Entrega::whereHas('tarea', function ($query) use ($cursosIds) {
                                        $query->whereIn('curso_id', $cursosIds);
                                    })
                                    ->with(['estudiante', 'tarea.curso'])
                                    ->orderBy('created_at', 'desc')
                                    ->limit(5)
                                    ->get();
        }

        // --- DATOS PARA GRÁFICAS ---

        // 1. Datos para Gráfica: Distribución de Estudiantes por Curso
        // Filtrar cursos que realmente tengan estudiantes para que la gráfica no sea muy grande si hay muchos cursos sin alumnos
        $cursosParaGraficaEstudiantes = $cursosDocente->filter(function ($curso) {
            return $curso->estudiantes_activos_count > 0;
        });
        $chartEstudiantesPorCursoLabels = $cursosParaGraficaEstudiantes->pluck('titulo');
        $chartEstudiantesPorCursoData = $cursosParaGraficaEstudiantes->pluck('estudiantes_activos_count');

        // 2. Datos para Gráfica: Estado de Calificación de Entregas (General)
        $entregasTotalesCursosDocente = 0;
        $entregasCalificadasCursosDocente = 0;
        if ($cursosIds->isNotEmpty()) {
            $entregasTotalesCursosDocente = Entrega::whereHas('tarea', function ($query) use ($cursosIds) {
                                                    $query->whereIn('curso_id', $cursosIds);
                                                })->count();
            $entregasCalificadasCursosDocente = Entrega::whereHas('tarea', function ($query) use ($cursosIds) {
                                                    $query->whereIn('curso_id', $cursosIds);
                                                })
                                                ->whereNotNull('calificacion')
                                                ->count();
        }
        $chartEstadoEntregasLabels = ['Calificadas', 'Pendientes de Calificar'];
        $entregasPendientesData = $entregasTotalesCursosDocente - $entregasCalificadasCursosDocente;
        // Asegurarse que no sea negativo si no hay entregas totales pero sí calificadas (caso raro)
        $entregasPendientesData = $entregasPendientesData < 0 ? 0 : $entregasPendientesData;

        $chartEstadoEntregasData = [
            $entregasCalificadasCursosDocente,
            $entregasPendientesData
        ];

        return view('docente.dashboard', compact(
            'cursosDocente',
            'solicitudesPendientesCount',
            'totalCursosActivos',
            'totalEstudiantesInscritos',
            'totalTareasSinCalificar',
            'ultimasEntregas',
            'chartEstudiantesPorCursoLabels',
            'chartEstudiantesPorCursoData',
            'chartEstadoEntregasLabels',
            'chartEstadoEntregasData'
        ));
    }

    public function verTodosEstudiantes(Request $request): View {
        $docente = Auth::user();
        $cursosIdsDelDocente = $docente->cursosImpartidos()->pluck('cursos.id');
        $queryInscripciones = Inscripcion::whereIn('curso_id', $cursosIdsDelDocente)
                                        ->where('estado', 'activo')
                                        ->with(['estudiante', 'curso'])
                                        ->join('usuarios', 'inscripciones.estudiante_id', '=', 'usuarios.id')
                                        ->select('inscripciones.*');
        $terminoBusqueda = $request->input('busqueda');
        if ($terminoBusqueda) {
            $queryInscripciones->where(function ($q) use ($terminoBusqueda) {
                $q->where('usuarios.nombre', 'like', "%{$terminoBusqueda}%")
                  ->orWhere('usuarios.apellidos', 'like', "%{$terminoBusqueda}%")
                  ->orWhere('usuarios.email', 'like', "%{$terminoBusqueda}%");
            });
        }
        $inscripciones = $queryInscripciones->orderBy('usuarios.apellidos')->orderBy('usuarios.nombre')->paginate(20);
        return view('docente.estudiantes_generales.index', compact('inscripciones', 'terminoBusqueda'));
    }

    public function verEntregasPorCalificar(Request $request): View
    {
        $docente = Auth::user();
        $cursosIdsDelDocente = $docente->cursosImpartidos()->pluck('cursos.id');
        $queryEntregas = Entrega::whereNull('calificacion')
                                ->whereHas('tarea', function ($q_tarea) use ($cursosIdsDelDocente) {
                                    $q_tarea->whereIn('curso_id', $cursosIdsDelDocente);
                                })
                                ->with(['estudiante', 'tarea.curso'])
                                ->join('tareas', 'entregas.tarea_id', '=', 'tareas.id')
                                ->join('usuarios', 'entregas.estudiante_id', '=', 'usuarios.id')
                                ->select('entregas.*');
        $terminoBusqueda = $request->input('busqueda');
        if ($terminoBusqueda) {
            $queryEntregas->where(function ($q) use ($terminoBusqueda) {
                $q->where('usuarios.nombre', 'like', "%{$terminoBusqueda}%")
                  ->orWhere('usuarios.apellidos', 'like', "%{$terminoBusqueda}%")
                  ->orWhere('tareas.titulo', 'like', "%{$terminoBusqueda}%");
            });
        }
        $entregasPorCalificar = $queryEntregas->orderBy('entregas.created_at', 'asc')->paginate(15);
        return view('docente.entregas_por_calificar.index', compact('entregasPorCalificar', 'terminoBusqueda'));
    }
}
