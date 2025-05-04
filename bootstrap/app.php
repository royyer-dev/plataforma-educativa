<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware; // Asegúrate que Middleware esté importado

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php', // Ruta a tu archivo de rutas web
        commands: __DIR__.'/../routes/console.php', // Rutas para comandos artisan
        health: '/up', // Ruta para chequeos de salud
    )
    ->withMiddleware(function (Middleware $middleware) { // <-- Inicio de configuración de Middleware

        // vvv ESTA ES LA PARTE QUE AÑADES/ASEGURAS QUE EXISTA vvv
        // Define los alias para los middlewares de ruta
        $middleware->alias([
            'role'       => \App\Http\Middleware\CheckRole::class, // Alias para tu middleware de rol
            // Aquí puedes añadir otros alias que necesites en el futuro
            // 'verified'   => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class, // Ejemplo: para verificación de email
            // 'guest'      => \App\Http\Middleware\RedirectIfAuthenticated::class, // Ejemplo: para redirigir si ya está logueado
        ]);
        // ^^^ FIN DE LA PARTE IMPORTANTE ^^^

        // Aquí podrías añadir otras configuraciones globales o de grupo si fuera necesario
        // Ejemplo: $middleware->validateCsrfTokens(except: [ ... ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Configuración del manejo de excepciones (déjalo como está por ahora)
        // Se pueden personalizar las respuestas para diferentes errores aquí
    })->create(); // Crea y retorna la instancia de la aplicación