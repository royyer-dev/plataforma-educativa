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
    /**
     * Muestra el dashboard principal del docente.
     * Incluye cursos impartidos y solicitudes pendientes.
     */
    public function index(): View
    {
        $docente = Auth::user();

        // 1. Cursos que imparte el docente (para la lista y estadísticas)
        $cursosDocente = $docente->cursosImpartidos()->orderBy('titulo')->get();
        // vvv CORRECCIÓN: Especificar la tabla para 'id' vvv
        $cursosIds = $docente->cursosImpartidos()->pluck('cursos.id'); // Solo IDs de los cursos

        // 2. Solicitudes Pendientes (conteo)
        $solicitudesPendientesCount = 0;
        if ($cursosIds->isNotEmpty()) {
             $solicitudesPendientesCount = Inscripcion::where('estado', 'pendiente')
                                                ->whereIn('curso_id', $cursosIds)
                                                ->count();
        }

        // 3. Estadísticas Adicionales
        $totalCursosActivos = $cursosDocente->where('estado', 'publicado')->count();

        $totalEstudiantesInscritos = 0;
        if ($cursosIds->isNotEmpty()) {
            $totalEstudiantesInscritos = Inscripcion::whereIn('curso_id', $cursosIds)
                                             ->where('estado', 'activo')
                                             ->distinct('estudiante_id')
                                             ->count('estudiante_id');
        }

        $totalTareasSinCalificar = 0;
        if ($cursosIds->isNotEmpty()) {
            // Contar entregas que pertenecen a tareas de los cursos del docente,
            // y que aún no tienen calificación.
            $totalTareasSinCalificar = Entrega::whereHas('tarea', function ($query) use ($cursosIds) {
                                                $query->whereIn('curso_id', $cursosIds);
                                            })
                                            ->whereNull('calificacion') // Donde la calificación es NULL
                                            ->count();
        }

        // 4. Actividad Reciente: Últimas 5 entregas recibidas en sus cursos
        $ultimasEntregas = collect(); // Colección vacía por defecto
        if ($cursosIds->isNotEmpty()) {
            $ultimasEntregas = Entrega::whereHas('tarea', function ($query) use ($cursosIds) {
                                        $query->whereIn('curso_id', $cursosIds);
                                    })
                                    ->with(['estudiante', 'tarea.curso']) // Cargar relaciones
                                    ->orderBy('created_at', 'desc')
                                    ->limit(5)
                                    ->get();
        }

        // Pasar los datos a la vista
        return view('docente.dashboard', compact(
            'cursosDocente',
            'solicitudesPendientesCount',
            'totalCursosActivos',
            'totalEstudiantesInscritos',
            'totalTareasSinCalificar',
            'ultimasEntregas'
        ));
    }

    /**
     * Muestra una lista general de todos los estudiantes inscritos
     * en los cursos del docente, con opción de búsqueda.
     * Ruta: GET /docente/mis-estudiantes
     * Nombre: docente.estudiantes.generales
     */
    public function verTodosEstudiantes(Request $request): View
    {
        $docente = Auth::user();
        // vvv CORRECCIÓN: Especificar la tabla para 'id' vvv
        $cursosIdsDelDocente = $docente->cursosImpartidos()->pluck('cursos.id');

        // Query base para las inscripciones activas en los cursos del docente
        $queryInscripciones = Inscripcion::whereIn('curso_id', $cursosIdsDelDocente)
                                        ->where('estado', 'activo')
                                        ->with(['estudiante', 'curso']) // Cargar relaciones
                                        ->join('usuarios', 'inscripciones.estudiante_id', '=', 'usuarios.id') // Join para buscar por nombre/email
                                        ->select('inscripciones.*'); // Seleccionar todo de inscripciones para evitar ambigüedad

        // Lógica de Búsqueda
        $terminoBusqueda = $request->input('busqueda');
        if ($terminoBusqueda) {
            $queryInscripciones->where(function ($q) use ($terminoBusqueda) {
                $q->where('usuarios.nombre', 'like', "%{$terminoBusqueda}%")
                  ->orWhere('usuarios.apellidos', 'like', "%{$terminoBusqueda}%")
                  ->orWhere('usuarios.email', 'like', "%{$terminoBusqueda}%");
            });
        }

        // Ordenar y paginar
        $inscripciones = $queryInscripciones->orderBy('usuarios.apellidos')->orderBy('usuarios.nombre')->paginate(20);

        return view('docente.estudiantes_generales.index', compact('inscripciones', 'terminoBusqueda'));
    }

    public function verEntregasPorCalificar(Request $request): View
    {
        $docente = Auth::user();
        $cursosIdsDelDocente = $docente->cursosImpartidos()->pluck('cursos.id');

        // Query base para las entregas sin calificar en los cursos del docente
        $queryEntregas = Entrega::whereNull('calificacion') // Solo las que no tienen nota
                                ->whereHas('tarea', function ($q_tarea) use ($cursosIdsDelDocente) {
                                    $q_tarea->whereIn('curso_id', $cursosIdsDelDocente); // De tareas en sus cursos
                                })
                                ->with(['estudiante', 'tarea', 'tarea.curso']) // Cargar relaciones
                                ->join('tareas', 'entregas.tarea_id', '=', 'tareas.id') // Join para buscar por título de tarea
                                ->join('usuarios', 'entregas.estudiante_id', '=', 'usuarios.id') // Join para buscar por estudiante
                                ->select('entregas.*'); // Seleccionar todo de entregas

        // Lógica de Búsqueda (por nombre de estudiante o título de tarea)
        $terminoBusqueda = $request->input('busqueda');
        if ($terminoBusqueda) {
            $queryEntregas->where(function ($q) use ($terminoBusqueda) {
                $q->where('usuarios.nombre', 'like', "%{$terminoBusqueda}%")
                  ->orWhere('usuarios.apellidos', 'like', "%{$terminoBusqueda}%")
                  ->orWhere('tareas.titulo', 'like', "%{$terminoBusqueda}%");
            });
        }

        // Ordenar por fecha de entrega (más antiguas primero para calificar) y paginar
        $entregasPorCalificar = $queryEntregas->orderBy('entregas.created_at', 'asc')->paginate(15);

        return view('docente.entregas_por_calificar.index', compact('entregasPorCalificar', 'terminoBusqueda'));
    }
}
