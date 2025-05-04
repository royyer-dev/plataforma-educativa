<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Para obtener el docente
use App\Models\Inscripcion;          // Modelo Inscripcion
use App\Models\Curso;                // Modelo Curso (para autorización)
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse; // Para el tipo de retorno
// --- vvv Imports para Notificaciones vvv ---
use App\Notifications\InscripcionAprobadaNotification;
use App\Notifications\InscripcionRechazadaNotification;
// --- ^^^ Fin Imports ^^^ ---

class SolicitudController extends Controller
{
    /**
     * Constructor opcional para aplicar middleware.
     */
    // public function __construct()
    // {
    //     $this->middleware(['auth', 'role:docente']);
    // }

    /**
     * Muestra una lista de las solicitudes de inscripción pendientes.
     */
    public function index(): View
    {
        $docente = Auth::user();
        $cursosIds = $docente->cursosImpartidos()->pluck('cursos.id');
        $solicitudesPendientes = Inscripcion::where('estado', 'pendiente')
                                            ->whereIn('curso_id', $cursosIds)
                                            ->with(['estudiante', 'curso'])
                                            ->orderBy('created_at')
                                            ->paginate(15);
        return view('docente.solicitudes.index', compact('solicitudesPendientes'));
    }

    /**
     * Aprueba una solicitud de inscripción pendiente.
     */
    public function aprobar(Request $request, Inscripcion $inscripcion): RedirectResponse
    {
        // Verificar que esté pendiente
        if ($inscripcion->estado !== 'pendiente') {
            return redirect()->route('docente.solicitudes.index')
                             ->with('error', 'Esta solicitud ya no está pendiente.');
        }
        // Verificar que el curso exista y el docente tenga permiso
        $this->authorizeTeacherAccess($inscripcion->curso);

        // Guardar el estudiante ANTES de actualizar (importante para la notificación)
        $estudiante = $inscripcion->estudiante;

        // Actualizar estado de la inscripción
        $inscripcion->estado = 'activo';
        $inscripcion->save();

        // --- vvv Enviar Notificación de Aprobación vvv ---
        if ($estudiante) { // Comprobar si la relación estudiante se cargó correctamente
             $estudiante->notify(new InscripcionAprobadaNotification($inscripcion));
        }
        // --- ^^^ Fin Enviar Notificación ^^^ ---

        // Redirigir con mensaje de éxito
        return redirect()->route('docente.solicitudes.index')
                         ->with('status', '¡Solicitud aprobada! El estudiante ha sido inscrito.');
    }

    /**
     * Rechaza (elimina) una solicitud de inscripción pendiente.
     */
    public function rechazar(Request $request, Inscripcion $inscripcion): RedirectResponse
    {
        // Verificar que esté pendiente
        if ($inscripcion->estado !== 'pendiente') {
            return redirect()->route('docente.solicitudes.index')
                             ->with('error', 'Esta solicitud ya no está pendiente.');
        }
         // Verificar que el curso exista y el docente tenga permiso
        $this->authorizeTeacherAccess($inscripcion->curso);

        // Guardar estudiante y curso ANTES de borrar la inscripción (para la notificación)
        $estudiante = $inscripcion->estudiante;
        $curso = $inscripcion->curso; // Guardamos el curso antes de que se pierda la relación al borrar

        // Eliminar la inscripción pendiente
        $inscripcion->delete();

        // --- vvv Enviar Notificación de Rechazo vvv ---
        if ($estudiante && $curso) { // Comprobar si las relaciones se cargaron bien
             // Pasamos el objeto Curso a la notificación de rechazo
             $estudiante->notify(new InscripcionRechazadaNotification($curso));
        }
        // --- ^^^ Fin Enviar Notificación ^^^ ---

        // Redirigir con mensaje de éxito/informativo
        return redirect()->route('docente.solicitudes.index')
                         ->with('status', 'Solicitud de inscripción rechazada.');
    }


    // --- Método auxiliar para autorización ---
    protected function authorizeTeacherAccess(?Curso $curso): void // Usamos ?Curso para permitir null temporalmente
    {
        if (!$curso) {
             abort(404, 'Curso no encontrado para esta solicitud.');
        }
        if (!$curso->profesores()->where('profesor_id', Auth::id())->exists()) {
            abort(403, 'No tienes permiso para gestionar solicitudes de este curso.');
        }
    }
}
