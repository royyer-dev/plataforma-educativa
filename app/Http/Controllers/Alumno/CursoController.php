<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use App\Models\Curso;
use App\Models\Inscripcion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Notifications\NuevaSolicitudInscripcionNotification;
use App\Notifications\AlumnoSalioCursoNotification; // <-- Importar notificación de salida

class CursoController extends Controller
{
    /**
     * Muestra una lista de TODOS los cursos publicados disponibles.
     */
    public function index(): View
    {
        $estudiante = Auth::user();
        $cursosDisponibles = Curso::where('estado', 'publicado')
                                  ->with('profesores')
                                  ->orderBy('titulo')
                                  ->paginate(12);
        $inscripcionesEstudiante = $estudiante->inscripciones()
                                              ->get()
                                              ->keyBy('curso_id');
        return view('alumno.cursos.index', compact('cursosDisponibles', 'inscripcionesEstudiante'));
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
             return redirect()->route('alumno.cursos.index')
                              ->with('error', 'No estás inscrito en este curso o tu inscripción no está activa.');
        }

        $curso->load([
            'profesores',
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
            return redirect()->route('alumno.cursos.index')
                             ->with('error', 'Este curso no está disponible para inscripción.');
        }

        $existeInscripcion = $estudiante->inscripciones()
                                        ->where('curso_id', $curso->id)
                                        ->exists();

        if ($existeInscripcion) {
             return redirect()->route('alumno.cursos.index')
                              ->with('error', 'Ya tienes una inscripción o solicitud para este curso.');
        }

        // Crear la inscripción pendiente
        $inscripcion = $estudiante->inscripciones()->create([
             'curso_id' => $curso->id,
             'estado' => 'pendiente',
        ]);

        // --- Notificar a los Profesores del Curso ---
        $inscripcion->load(['estudiante', 'curso']);
        $profesores = $curso->profesores;

        if ($profesores->isNotEmpty()) {
            foreach ($profesores as $profesor) {
                if ($profesor instanceof \App\Models\Usuario) {
                     $profesor->notify(new NuevaSolicitudInscripcionNotification($inscripcion));
                }
            }
        }
        // --- Fin Notificar Profesores ---

        return redirect()->route('alumno.cursos.index')
                         ->with('status', '¡Solicitud de inscripción enviada exitosamente! Recibirás una notificación cuando sea aprobada.');
    }

    /**
     * Permite a un estudiante darse de baja (eliminar su inscripción) de un curso.
     */
    public function salirDelCurso(Request $request, Curso $curso): RedirectResponse
    {
        $estudiante = Auth::user();

        // 1. Validar contraseña
        $request->validate(['password_confirmacion' => 'required|string']);

        // 2. Verificar contraseña
        if (!Hash::check($request->input('password_confirmacion'), $estudiante->password)) {
            return back()->withErrors(['password_confirmacion' => 'La contraseña ingresada es incorrecta.'])->withInput();
        }

        // 3. Buscar inscripción
        $inscripcion = $estudiante->inscripciones()->where('curso_id', $curso->id)->first();

        // 4. Verificar si existe
        if (!$inscripcion) {
             return redirect()->route('alumno.cursos.index')
                              ->with('error', 'No se encontró una inscripción para este curso.');
        }

        // --- vvv Notificar ANTES de borrar la inscripción vvv ---
        // Cargar profesores del curso
        $profesores = $curso->profesores; // Asume que la relación existe y está cargada o se cargará
        if ($profesores->isNotEmpty()) {
            foreach ($profesores as $profesor) {
                 if ($profesor instanceof \App\Models\Usuario) {
                     // Pasamos el curso y el estudiante que sale
                     $profesor->notify(new AlumnoSalioCursoNotification($curso, $estudiante));
                 }
            }
        }
        // --- ^^^ Fin Notificar Profesores ^^^ ---

        // 5. Eliminar la inscripción
        $inscripcion->delete(); // Ahora sí borramos

        // 6. Redirigir
        return redirect()->route('alumno.cursos.index')
                         ->with('status', 'Has salido del curso "' . $curso->titulo . '" exitosamente.');
    }
}
