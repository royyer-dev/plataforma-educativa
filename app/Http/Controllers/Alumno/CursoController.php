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

class CursoController extends Controller
{
    /**
     * Muestra una lista de los cursos publicados disponibles PARA UNA CARRERA ESPECÍFICA.
     * Indica el estado de inscripción del estudiante actual para cada curso.
     * Ruta: GET /alumno/carreras/{carrera}/cursos
     * Nombre: alumno.cursos.index
     */
    public function index(Carrera $carrera): View // <-- $carrera se recibe aquí por Route Model Binding
    {
        $estudiante = Auth::user();

        // Obtener los cursos publicados que pertenecen a la carrera especificada
        $cursosDeLaCarrera = $carrera->cursos() // Usa la relación definida en el modelo Carrera
                                      ->where('estado', 'publicado')
                                      ->with('profesores') // Cargar profesores para mostrar
                                      ->orderBy('titulo')
                                      ->paginate(12); // Paginar resultados

        // Obtener TODAS las inscripciones del estudiante actual
        $inscripcionesEstudiante = $estudiante->inscripciones()
                                              ->get()
                                              ->keyBy('curso_id');

        // Pasar la carrera, sus cursos, y las inscripciones del estudiante a la vista
        return view('alumno.cursos.index', compact('carrera', 'cursosDeLaCarrera', 'inscripcionesEstudiante'));
    }

    /**
     * Muestra los detalles (contenido) de un curso específico si el estudiante está inscrito y activo.
     */
    public function show(Curso $curso): View | RedirectResponse
    {
        $estudiante = Auth::user();
        $inscripcion = $estudiante->inscripciones()
                                  ->where('curso_id', $curso->id)
                                  ->where('estado', 'activo')
                                  ->first();

        if (!$inscripcion) {
             return redirect()->route('alumno.carreras.index') // Redirige a la lista de carreras
                              ->with('error', 'No estás inscrito en este curso o tu inscripción no está activa.');
        }

        $curso->load([
            'profesores', 'carrera', // Asegúrate de cargar 'carrera' también
            'modulos' => function ($query) { $query->orderBy('orden'); },
            'modulos.materiales' => function ($query) { $query->orderBy('orden'); },
            'modulos.tareas' => function ($query) { $query->orderBy('fecha_limite'); },
            'materiales' => function ($query) { $query->whereNull('modulo_id')->orderBy('orden'); },
            'tareas' => function ($query) { $query->whereNull('modulo_id')->orderBy('fecha_limite'); }
        ]);

        return view('alumno.cursos.show', compact('curso', 'inscripcion'));
    }

    /**
     * Procesa la solicitud de inscripción de un estudiante a un curso.
     */
    public function solicitarInscripcion(Request $request, Curso $curso): RedirectResponse
    {
        $estudiante = Auth::user();

        if ($curso->estado !== 'publicado') {
            // Redirige a la lista de cursos de la carrera específica
            return redirect()->route('alumno.cursos.index', ['carrera' => $curso->carrera_id])
                             ->with('error', 'Este curso no está disponible para inscripción.');
        }

        $existeInscripcion = $estudiante->inscripciones()
                                        ->where('curso_id', $curso->id)
                                        ->exists();

        if ($existeInscripcion) {
             // Redirige a la lista de cursos de la carrera específica
             return redirect()->route('alumno.cursos.index', ['carrera' => $curso->carrera_id])
                              ->with('error', 'Ya tienes una inscripción o solicitud para este curso.');
        }

        $inscripcion = $estudiante->inscripciones()->create([
             'curso_id' => $curso->id,
             'estado' => 'pendiente',
        ]);

        $inscripcion->load(['estudiante', 'curso.profesores']);
        $profesores = $inscripcion->curso->profesores;

        if ($profesores->isNotEmpty()) {
            foreach ($profesores as $profesor) {
                if ($profesor instanceof \App\Models\Usuario) {
                     $profesor->notify(new NuevaSolicitudInscripcionNotification($inscripcion));
                }
            }
        }

        // Redirige a la lista de cursos de la carrera específica
        return redirect()->route('alumno.cursos.index', ['carrera' => $curso->carrera_id])
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
