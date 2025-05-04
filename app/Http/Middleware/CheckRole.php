<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Importar Auth
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string ...$roles  // Acepta uno o más nombres de rol como argumento
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Primero, verifica si el usuario está autenticado
        if (!Auth::check()) {
            return redirect('login'); // O abort(401);
        }

        $user = Auth::user();

        // Verifica si el usuario tiene alguno de los roles requeridos
        // Usamos la relación roles() del modelo Usuario
        if (!$user->roles()->whereIn('nombre', $roles)->exists()) {
            // Si no tiene el rol, abortamos con error 403 (Prohibido)
            abort(403, 'Acceso no autorizado para tu rol.');
            // Alternativa: Redirigir a otra página con un mensaje de error
            // return redirect('/home')->with('error', 'No tienes permiso para acceder.');
        }

        // Si tiene el rol, permite que la solicitud continúe
        return $next($request);
    }
}