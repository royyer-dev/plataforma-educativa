<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario; // Usar el modelo Usuario
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Role; // Modelo Role para buscar roles
use Illuminate\Http\Request; // Necesario para el método showRegistrationForm con Request
use Illuminate\View\View; // Para el tipo de retorno de showRegistrationForm
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

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     * (La redirección real se maneja en LoginController->authenticated)
     * @var string
     */
    protected $redirectTo = '/home'; // O a donde prefieras post-registro

    /**
     * Create a new controller instance.
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Muestra el formulario de registro de la aplicación.
     * Sobrescribimos para pasar los roles a la vista.
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm(): View // Ajustado tipo de retorno
    {
        // Obtenemos solo los roles que queremos permitir en el registro público
        $roles = Role::whereIn('nombre', ['estudiante', 'docente'])->pluck('nombre', 'id');
        return view('auth.register', compact('roles'));
    }

    /**
     * Get a validator for an incoming registration request.
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data): ValidatorContract
    {
        // Asegúrate que las claves coincidan con los 'name' de tus inputs
        return Validator::make($data, [
            'nombre' => ['required', 'string', 'max:150'],
            'apellidos' => ['nullable', 'string', 'max:150'],
            'telefono' => ['nullable', 'string', 'max:20', 'regex:/^[0-9\s\-\+\(\)]*$/'], // Regla simple para teléfono
            'genero' => ['nullable', 'string', 'in:masculino,femenino,otro,no_especificado'], // <-- Validación para Género
            'email' => ['required', 'string', 'email', 'max:255', 'unique:usuarios'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
        ], [
            // Mensajes personalizados (opcional)
            'nombre.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El formato del correo no es válido.',
            'email.unique' => 'Este correo electrónico ya está en uso.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos :min caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'role_id.required' => 'Debes seleccionar un rol.',
            'role_id.exists' => 'El rol seleccionado no es válido.',
            'genero.in' => 'El valor seleccionado para género no es válido.',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     * @param  array  $data
     * @return \App\Models\Usuario
     */
    protected function create(array $data): Usuario
    {
        $usuario = Usuario::create([
            'nombre' => $data['nombre'],
            'apellidos' => $data['apellidos'] ?? null,
            'telefono' => $data['telefono'] ?? null,
            'genero' => $data['genero'] ?? null, // <-- Guardar Género
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            // ruta_foto_perfil se manejará por separado o tendrá un default
        ]);

        // Adjunta el rol seleccionado
        $usuario->roles()->attach($data['role_id']);

        return $usuario;
    }
}
