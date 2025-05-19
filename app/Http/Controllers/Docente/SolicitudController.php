<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Inscripcion;
use App\Models\Curso;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Notifications\InscripcionAprobadaNotification;
use App\Notifications\InscripcionRechazadaNotification;
// Asegúrate de importar Carrera si lo usas directamente para type hints o consultas
// use App\Models\Carrera;

class SolicitudController extends Controller
{
    /**
     * Muestra lista de solicitudes pendientes y aceptadas, incluyendo la carrera del curso.
     */
    public function index(): View
    {
        $docente = Auth::user();
        $cursosIdsDelDocente = $docente->cursosImpartidos()->pluck('cursos.id');

        // 1. Buscar inscripciones con estado 'pendiente'
        $solicitudesPendientes = Inscripcion::where('estado', 'pendiente')
                                            ->whereIn('curso_id', $cursosIdsDelDocente)
                                            // vvv MODIFICADO: Cargar también la carrera del curso vvv
                                            ->with(['estudiante', 'curso.carrera'])
                                            // ^^^ FIN MODIFICACIÓN ^^^
                                            ->orderBy('created_at', 'desc')
                                            ->paginate(10, ['*'], 'pendientes');

        // 2. Buscar inscripciones con estado 'activo' (aceptadas)
        $solicitudesAceptadas = Inscripcion::where('estado', 'activo')
                                           ->whereIn('curso_id', $cursosIdsDelDocente)
                                           // vvv MODIFICADO: Cargar también la carrera del curso vvv
                                           ->with(['estudiante', 'curso.carrera'])
                                           // ^^^ FIN MODIFICACIÓN ^^^
                                           ->orderBy('updated_at', 'desc')
                                           ->paginate(10, ['*'], 'aceptadas');

        return view('docente.solicitudes.index', compact('solicitudesPendientes', 'solicitudesAceptadas'));
    }

    // ... (métodos aprobar, rechazar y authorizeTeacherAccess sin cambios funcionales para esto) ...
    public function aprobar(Request $request, Inscripcion $inscripcion): RedirectResponse
    {
        if ($inscripcion->estado !== 'pendiente') {
            return redirect()->route('docente.solicitudes.index')
                             ->with('error', 'Esta solicitud ya no está pendiente.');
        }
        $this->authorizeTeacherAccess($inscripcion->curso);

        $estudiante = $inscripcion->estudiante;
        $inscripcion->estado = 'activo';
        $inscripcion->fecha_inscripcion = now();
        $inscripcion->save();

        if ($estudiante) {
             $estudiante->notify(new InscripcionAprobadaNotification($inscripcion));
        }
        return redirect()->route('docente.solicitudes.index')
                         ->with('status', '¡Solicitud aprobada! El estudiante ha sido inscrito.');
    }

    public function rechazar(Request $request, Inscripcion $inscripcion): RedirectResponse
    {
        if ($inscripcion->estado !== 'pendiente') {
            return redirect()->route('docente.solicitudes.index')
                             ->with('error', 'Esta solicitud ya no está pendiente.');
        }
        $this->authorizeTeacherAccess($inscripcion->curso);

        $estudiante = $inscripcion->estudiante;
        $curso = $inscripcion->curso;
        $inscripcion->delete();

        if ($estudiante && $curso) {
             $estudiante->notify(new InscripcionRechazadaNotification($curso));
        }
        return redirect()->route('docente.solicitudes.index')
                         ->with('status', 'Solicitud de inscripción rechazada.');
    }

    protected function authorizeTeacherAccess(?Curso $curso): void
    {
        if (!$curso) {
             abort(404, 'Curso no encontrado para esta solicitud.');
        }
        if (!$curso->profesores()->where('profesor_id', Auth::id())->exists()) {
            abort(403, 'No tienes permiso para gestionar solicitudes de este curso.');
        }
    }
}
