<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request; // <-- AÑADIDO: Necesario para el método authenticated
use Illuminate\Support\Facades\Auth; // <-- AÑADIDO: Necesario para usar Auth si fuera preciso (aunque $user ya viene inyectado)

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers; // Este Trait contiene la lógica principal de login

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    // protected $redirectTo = '/home'; // <-- ELIMINADO o COMENTADO: La redirección ahora es dinámica

    /**
     * Create a new controller instance.
     * Aplica middlewares: 'guest' para todo excepto logout, 'auth' solo para logout.
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    // --- vvv MÉTODO AÑADIDO vvv ---
    /**
     * The user has been authenticated.
     * Este método se llama automáticamente después de una autenticación exitosa.
     * Sobrescribimos el método del trait AuthenticatesUsers para redirigir según el rol.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user // Laravel inyecta aquí al usuario autenticado (debe ser tu modelo App\Models\Usuario)
     * @return \Illuminate\Http\RedirectResponse // Debe devolver una redirección
     */
    protected function authenticated(Request $request, $user)
    {
        // Si hay una URL intencionada guardada en la sesión, usar esa
        if ($redirectTo = session()->pull('url.intended')) {
            return redirect()->to($redirectTo);
        }

        // Si no hay URL intencionada, redirigir según el rol
        $rol = $user->roles()->first();
        
        if ($rol) {
            switch ($rol->nombre) {
                case 'docente':
                    return redirect()->route('docente.dashboard');
                case 'estudiante':
                    return redirect()->route('alumno.dashboard');
                default:
                    return redirect('/home');
            }
        }

        return redirect('/home');
    }
    // --- ^^^ FIN MÉTODO AÑADIDO ^^^ ---

} // Fin de la clase LoginController