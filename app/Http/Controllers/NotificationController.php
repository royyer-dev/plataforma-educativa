<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification; // Modelo para notificaciones de BD
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
// Añadir modelos si necesitas verificar recursos en 'index' o 'destroy'
// use App\Models\Curso;
// use App\Models\Tarea;
// etc.

class NotificationController extends Controller
{
    /**
     * Muestra todas las notificaciones del usuario autenticado, paginadas.
     * Ruta: GET /notifications
     * Nombre: notifications.index
     */
    public function index(): View
    {
        $user = Auth::user();
        $notifications = $user->notifications()
                              ->orderBy('created_at', 'desc')
                              ->paginate(15);

        // Opcional: Marcar como leídas al visitar (descomentar si se desea)
        // $user->unreadNotifications->markAsRead();

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Marca una notificación específica como leída y redirige a su URL asociada.
     * Ruta: GET /notifications/{notification}/read
     * Nombre: notifications.read
     */
    public function markAsReadAndRedirect(DatabaseNotification $notification): RedirectResponse
    {
        // Verificar pertenencia
        if (Auth::id() !== $notification->notifiable_id) {
            abort(403);
        }

        // Marcar como leída
        $notification->markAsRead();

        // Obtener URL (redirigir a index si no hay URL específica)
        $redirectUrl = $notification->data['url'] ?? route('notifications.index');

        // Aquí podrías re-implementar la verificación de existencia del recurso si lo deseas
        // (Aunque decidimos no hacerlo antes para simplificar)

        return redirect($redirectUrl);
    }

     /**
     * Marca todas las notificaciones no leídas del usuario como leídas.
     * Ruta: PATCH /notifications/mark-all-read (o POST)
     * Nombre: notifications.markAllRead
     */
    public function markAllAsRead(): RedirectResponse // <-- Método Descomentado y Completado
    {
        // Marca todas las notificaciones NO leídas del usuario actual como leídas
        Auth::user()->unreadNotifications->markAsRead();

        // Redirige de vuelta a la página anterior (probablemente el historial)
        return back()->with('status', 'Todas las notificaciones marcadas como leídas.');
    }

     /**
     * Elimina una notificación específica.
     * Ruta: DELETE /notifications/{notification}
     * Nombre: notifications.destroy
     */
    public function destroy(DatabaseNotification $notification): RedirectResponse // <-- Método Descomentado y Completado
    {
         // Verificar que la notificación pertenezca al usuario autenticado
         if (Auth::id() !== $notification->notifiable_id) {
            abort(403); // No autorizado
        }

        // Eliminar la notificación
        $notification->delete();

        // Redirige de vuelta a la página anterior (probablemente el historial)
        return back()->with('status', 'Notificación eliminada.');
    }

}
