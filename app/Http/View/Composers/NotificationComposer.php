<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth; // Para obtener el usuario autenticado


class NotificationComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view): void
    {
        $unreadNotifications = collect(); // Colección vacía por defecto
        $user = Auth::user();

        // Si hay un usuario autenticado, obtener sus notificaciones no leídas
        if ($user) {
            // Obtener las notificaciones no leídas (puedes limitar cuántas traer: ->take(5))
            $unreadNotifications = $user->unreadNotifications;
        }

        // Pasar la colección de notificaciones no leídas a la vista
        $view->with('unreadNotifications', $unreadNotifications);
    }
}
