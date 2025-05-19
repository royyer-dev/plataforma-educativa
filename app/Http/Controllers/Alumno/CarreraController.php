<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use App\Models\Curso;
use App\Models\Inscripcion;
use App\Models\Carrera; // <-- Asegúrate que este import esté presente
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Notifications\NuevaSolicitudInscripcionNotification;
use App\Notifications\AlumnoSalioCursoNotification;
use Illuminate\Support\Collection;
use Carbon\Carbon; // Para manejo de fechas
use App\Models\Tarea; // Asegúrate de importar el modelo Tarea

class CarreraController extends Controller
{
    /**
     * Muestra una lista de todas las carreras que tienen cursos publicados.
     * Ruta: GET /alumno/carreras
     * Nombre: alumno.carreras.index
     */
    public function index(): View
    {
        $estudiante = Auth::user();

        // 1. Obtener las inscripciones ACTIVAS del estudiante con sus cursos y carreras
        $inscripcionesActivas = $estudiante->inscripciones()
            ->where('estado', 'activo')
            ->with(['curso.carrera', 'curso.profesores']) // Eager load para eficiencia
            ->get();

        // 2. Agrupar los CURSOS ACTIVOS por el ID de su carrera
        $cursosActivosPorCarreraId = $inscripcionesActivas
            ->map(function ($inscripcion) {
                return $inscripcion->curso; // Nos interesa el objeto Curso
            })
            ->filter(function ($curso) { // Asegurarse que el curso y su carrera existan
                return $curso && $curso->carrera;
            })
            ->groupBy(function ($curso) {
                return $curso->carrera->id; // Agrupar por ID de la carrera
            });

        // 3. Obtener los modelos Carrera para las carreras donde el estudiante tiene cursos activos
        $idsCarrerasConCursosActivos = $cursosActivosPorCarreraId->keys()->all();
        $carrerasConCursosActivos = collect();
        if (!empty($idsCarrerasConCursosActivos)) {
            $carrerasConCursosActivos = Carrera::whereIn('id', $idsCarrerasConCursosActivos)
                                        ->orderBy('nombre')
                                        ->get();
        }

        // 4. Obtener TODAS las carreras que tienen al menos un curso PUBLICADO,
        //    para la sección "Explorar Otras Carreras".
        //    También contamos los cursos publicados para cada una.
        $todasLasCarrerasParaExplorar = Carrera::whereHas('cursos', function ($query) {
                $query->where('estado', 'publicado');
            })
            ->withCount(['cursos' => function ($query) { // Contar solo cursos publicados
                $query->where('estado', 'publicado');
            }])
            ->orderBy('nombre')
            ->get();

        return view('alumno.carreras.index', compact(
            'carrerasConCursosActivos',
            'cursosActivosPorCarreraId',
            'todasLasCarrerasParaExplorar'
        ));
    }

    /**
     * Procesa la solicitud de inscripción de un estudiante a un curso.
     */
    public function solicitarInscripcion(Request $request, Curso $curso): RedirectResponse
    {
        $estudiante = Auth::user();

        if ($curso->estado !== 'publicado') {
            return redirect()->route('alumno.carreras.cursos.index', $curso->carrera_id) // Corregido para volver a cursos de la carrera
                             ->with('error', 'Este curso no está disponible para inscripción.');
        }

        $existeInscripcion = $estudiante->inscripciones()
                                        ->where('curso_id', $curso->id)
                                        ->exists();

        if ($existeInscripcion) {
             return redirect()->route('alumno.carreras.cursos.index', $curso->carrera_id) // Corregido
                              ->with('error', 'Ya tienes una inscripción o solicitud para este curso.');
        }

        $inscripcion = $estudiante->inscripciones()->create([
             'curso_id' => $curso->id,
             'estado' => 'pendiente',
        ]);

        $inscripcion->load(['estudiante', 'curso.profesores']); // Asegurar que profesores se cargue a través de curso
        $profesores = $inscripcion->curso->profesores;

        if ($profesores->isNotEmpty()) {
            foreach ($profesores as $profesor) {
                if ($profesor instanceof \App\Models\Usuario) {
                     $profesor->notify(new NuevaSolicitudInscripcionNotification($inscripcion));
                }
            }
        }

        return redirect()->route('alumno.carreras.cursos.index', $curso->carrera_id) // Corregido
                         ->with('status', '¡Solicitud de inscripción enviada exitosamente!');
    }

    /**
     * Permite a un estudiante darse de baja de un curso.
     */
    public function salirDelCurso(Request $request, Curso $curso): RedirectResponse
    {
        $estudiante = Auth::user();
        $request->validate(['password_confirmacion' => 'required|string']);

        if (!Hash::check($request->input('password_confirmacion'), $estudiante->password)) {
            return back()->withErrors(['password_confirmacion' => 'La contraseña ingresada es incorrecta.'])->withInput();
        }

        $inscripcion = $estudiante->inscripciones()->where('curso_id', $curso->id)->first();

        if (!$inscripcion) {
             return redirect()->route('alumno.carreras.index') // Redirige a la lista de carreras
                              ->with('error', 'No se encontró una inscripción para este curso.');
        }

        $profesores = $curso->profesores;
        if ($profesores->isNotEmpty()) {
            foreach ($profesores as $profesor) {
                 if ($profesor instanceof \App\Models\Usuario) {
                     $profesor->notify(new AlumnoSalioCursoNotification($curso, $estudiante));
                 }
            }
        }
        $inscripcion->delete();

        return redirect()->route('alumno.carreras.index') // Redirige a la lista de carreras
                         ->with('status', 'Has salido del curso "' . $curso->titulo . '" exitosamente.');
    }
}
