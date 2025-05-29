<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View; // <-- Asegúrate que esté importado
use App\Http\View\Composers\NotificationComposer; // <-- Asegúrate que esté importado

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Forzar HTTPS solo en producción
        if (config('app.env') === 'production') {
            \URL::forceScheme('https');
        }

        // --- vvv AÑADIR ESTA LÍNEA PARA REGISTRAR EL COMPOSER vvv ---
        View::composer('layouts.app', NotificationComposer::class);
        // --- ^^^ FIN LÍNEA AÑADIDA ^^^ ---

        // Aquí pueden ir otras configuraciones si las tienes
    }
}
