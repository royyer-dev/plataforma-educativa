<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Para obtener usuario actual
use Illuminate\Support\Facades\Hash; // Para verificar y crear hashes de contraseña
use Illuminate\Support\Facades\Validator; // Para validación manual (opcional)
use Illuminate\Validation\Rules\Password; // Reglas de contraseña más estrictas
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\Usuario; // Importar modelo Usuario

class PerfilController extends Controller
{
    /**
     * Muestra la página del perfil del usuario autenticado.
     * Ruta: GET /perfil
     * Nombre: perfil.show
     */
    public function show(): View
    {
        // Obtener el usuario autenticado
        $usuario = Auth::user();

        // Simplemente retornar la vista pasando el usuario
        return view('perfil.show', compact('usuario'));
    }

    /**
     * Actualiza la contraseña del usuario autenticado.
     * Ruta: PATCH /perfil/password
     * Nombre: perfil.updatePassword
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $usuario = Auth::user(); // Obtener el usuario autenticado

        // 1. Validar los datos del formulario
        $request->validate([
            'current_password' => ['required', 'string', function ($attribute, $value, $fail) use ($usuario) {
                // Validación personalizada para verificar la contraseña actual
                if (!Hash::check($value, $usuario->password)) {
                    $fail('La contraseña actual ingresada es incorrecta.');
                }
            }],
            'password' => [
                'required',
                'string',
                Password::min(8) // Requiere al menos 8 caracteres (puedes añadir ->letters(), ->mixedCase(), ->numbers(), ->symbols())
                          ->uncompromised(), // Verifica si la contraseña ha sido expuesta en brechas de datos (requiere conexión a internet)
                'confirmed' // Busca un campo llamado 'password_confirmation' que coincida
            ],
        ],[
            // Mensajes personalizados (opcional)
            'current_password.required' => 'Debes ingresar tu contraseña actual.',
            'password.required' => 'Debes ingresar una nueva contraseña.',
            'password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de la nueva contraseña no coincide.',
            'password.uncompromised' => 'Esta contraseña ha sido expuesta en brechas de datos, por favor elige otra.',
        ]);

        // 2. Si la validación pasa, actualizar la contraseña
        $usuario->forceFill([ // Usar forceFill para campos no masivamente asignables como 'password'
            'password' => Hash::make($request->input('password')),
        ])->save();

        // 3. Redirigir de vuelta al perfil con mensaje de éxito
        return redirect()->route('perfil.show')
                         ->with('status', '¡Contraseña actualizada exitosamente!');
    }

    // --- Método futuro para actualizar perfil (nombre, email, etc.) ---
    // public function updateProfile(Request $request): RedirectResponse
    // {
    //     $usuario = Auth::user();
    //     $validatedData = $request->validate([
    //         'nombre' => 'required|string|max:150',
    //         'apellidos' => 'nullable|string|max:150',
    //         'telefono' => 'nullable|string|max:20',
    //         // Validar email unique ignorando el usuario actual
    //         'email' => 'required|string|email|max:255|unique:usuarios,email,' . $usuario->id,
    //     ]);
    //
    //     // Verificar si el email cambió para posible re-verificación (más avanzado)
    //
    //     $usuario->update($validatedData);
    //
    //     return redirect()->route('perfil.show')->with('status', 'Perfil actualizado.');
    // }
}
