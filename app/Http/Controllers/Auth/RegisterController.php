<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario; // <-- CORREGIDO: Usar el modelo Usuario
use Illuminate\Foundation\Auth\RegistersUsers; // Trait para la lógica de registro
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Role; // Modelo Role para buscar roles
use Illuminate\Contracts\Validation\Validator as ValidatorContract; // Para type hint en validator()

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | Este controlador maneja el registro de nuevos usuarios, su validación
    | y creación. Usa el trait RegistersUsers para la funcionalidad principal.
    |
    */

    use RegistersUsers; // <-- AÑADIDO: ¡Esta línea es crucial! Usa el Trait

    /**
     * Where to redirect users after registration.
     * Puedes dejar '/home' o cambiarlo si prefieres redirigir a otro lugar
     * después del registro (aunque el método authenticated en LoginController
     * manejará la redirección después del login automático que ocurre tras el registro).
     *
     * @var string
     */
    protected $redirectTo = '/home'; // O puedes apuntar a una ruta de "registro exitoso"

    /**
     * Create a new controller instance.
     * Aplica el middleware 'guest' para que solo usuarios no logueados accedan.
     *
     * @return void
     */
    public function __construct() // <-- AÑADIDO: Constructor estándar
    {
        $this->middleware('guest');
    }

    /**
     * Muestra el formulario de registro de la aplicación.
     * Sobrescribimos para pasar los roles a la vista.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm() // <-- Este método ya lo tenías y estaba bien
    {
        // Obtenemos solo los roles que queremos permitir en el registro público
        $roles = Role::whereIn('nombre', ['estudiante', 'docente'])->pluck('nombre', 'id');
        return view('auth.register', compact('roles'));
    }

    /**
     * Get a validator for an incoming registration request.
     * Define las reglas de validación para los datos del formulario.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data): ValidatorContract // <-- AÑADIDO: Método de validación
    {
        // Asegúrate que las claves coincidan con los 'name' de tus inputs en el formulario
        return Validator::make($data, [
            'nombre' => ['required', 'string', 'max:150'],
            'apellidos' => ['nullable', 'string', 'max:150'], // Asume que es opcional
            'telefono' => ['nullable', 'string', 'max:20'],   // Asume que es opcional
            'email' => ['required', 'string', 'email', 'max:255', 'unique:usuarios'], // Valida unicidad en tabla 'usuarios'
            'password' => ['required', 'string', 'min:8', 'confirmed'], // 'confirmed' busca un campo 'password_confirmation'
            'role_id' => ['required', 'integer', 'exists:roles,id'], // Valida que el role_id exista en la tabla 'roles'
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     * Crea el registro del usuario en la BD y le asigna el rol.
     *
     * @param  array  $data
     * @return \App\Models\Usuario
     */
    protected function create(array $data): Usuario // <-- AÑADIDO: Método de creación
    {
        // Crea el usuario con los datos validados
        $usuario = Usuario::create([
            'nombre' => $data['nombre'],
            'apellidos' => $data['apellidos'] ?? null, // Usa ?? null si el campo es opcional
            'telefono' => $data['telefono'] ?? null,  // Usa ?? null si el campo es opcional
            'email' => $data['email'],
            'password' => Hash::make($data['password']), // Hashea la contraseña
            // No incluimos role_id aquí porque es una relación
        ]);

        // Adjunta el rol seleccionado al usuario recién creado
        // Usa la relación 'roles()' definida en el modelo Usuario
        $usuario->roles()->attach($data['role_id']);

        // Retorna el objeto Usuario creado
        return $usuario;
    }
}