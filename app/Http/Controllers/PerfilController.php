<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Para obtener usuario actual
use Illuminate\Support\Facades\Hash; // Para verificar y crear hashes de contraseña
use Illuminate\Support\Facades\Storage; // <-- Añadido para manejo de archivos
use Illuminate\Support\Facades\Validator; // Para validación manual (opcional)
use Illuminate\Validation\Rules\Password; // Reglas de contraseña más estrictas
use Illuminate\Validation\Rule; // <-- Añadido para validación unique
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\Usuario; // Importar modelo Usuario
use Illuminate\Support\Facades\DB; // Para transacciones
use Illuminate\Support\Facades\Log; // Para logging de errores

class PerfilController extends Controller
{
    /**
     * Muestra la página del perfil del usuario autenticado.
     * Ruta: GET /perfil
     * Nombre: perfil.show
     */
    public function show(): View
    {
        $usuario = Auth::user();
        return view('perfil.show', compact('usuario'));
    }

    /**
     * Actualiza la información básica del perfil del usuario autenticado.
     * Ruta: PATCH /perfil/update (o el nombre que le diste en web.php)
     * Nombre: perfil.update
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $usuario = Auth::user();

        // Validación de los datos del perfil
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:150',
            'apellidos' => 'nullable|string|max:150',
            'telefono' => 'nullable|string|max:20',
            // Validar email unique ignorando el ID del usuario actual
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('usuarios')->ignore($usuario->id),
            ],
            'genero' => 'nullable|string|in:masculino,femenino,otro,no_especificado',
        ],[
            'nombre.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El formato del correo no es válido.',
            'email.unique' => 'Este correo electrónico ya está en uso por otro usuario.',
            'genero.in' => 'El género seleccionado no es válido.',
        ]);

        // Si el email ha cambiado, podrías considerar invalidar la sesión actual
        // y requerir re-verificación de email si tienes esa funcionalidad.
        // Por ahora, solo actualizamos.
        // if ($request->email !== $usuario->email) {
        //     $validatedData['email_verified_at'] = null; // Ejemplo para forzar re-verificación
        // }

        $usuario->update($validatedData);

        return redirect()->route('perfil.show')
                         ->with('status_profile', '¡Información de perfil actualizada exitosamente!');
    }


    /**
     * Actualiza la foto de perfil del usuario autenticado.
     * Ruta: PATCH /perfil/picture (o el nombre que le diste en web.php)
     * Nombre: perfil.updatePicture
     */
    public function updatePicture(Request $request): RedirectResponse
    {
        $usuario = Auth::user();

        // Validación de la foto
        $request->validate([
            'foto_perfil' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
        ],[
            'foto_perfil.required' => 'Debes seleccionar una imagen.',
            'foto_perfil.image' => 'El archivo debe ser una imagen.',
            'foto_perfil.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif.',
            'foto_perfil.max' => 'La imagen no debe pesar más de 2MB.',
        ]);

        if ($request->hasFile('foto_perfil')) {
            // 1. Borrar foto antigua si existe
            if ($usuario->ruta_foto_perfil) {
                Storage::disk('public')->delete($usuario->ruta_foto_perfil);
            }

            // 2. Guardar nueva foto
            // Se guardará en 'storage/app/public/fotos_perfil/nombre_archivo.ext'
            // La URL pública será 'storage/fotos_perfil/nombre_archivo.ext'
            $path = $request->file('foto_perfil')->store('fotos_perfil', 'public');

            // 3. Actualizar la ruta en la base de datos
            $usuario->ruta_foto_perfil = $path;
            $usuario->save();

            return redirect()->route('perfil.show')
                             ->with('status_profile', '¡Foto de perfil actualizada exitosamente!');
        }

        return redirect()->route('perfil.show')
                         ->with('error_profile', 'No se pudo actualizar la foto de perfil.');
    }


    /**
     * Actualiza la contraseña del usuario autenticado.
     * Ruta: PATCH /perfil/password
     * Nombre: perfil.updatePassword
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $usuario = Auth::user();

        // Validar los datos del formulario de contraseña
        // Los errores se enviarán al 'default' error bag, que es lo que la vista espera
        $request->validate([
            'current_password' => ['required', 'string', function ($attribute, $value, $fail) use ($usuario) {
                if (!Hash::check($value, $usuario->password)) {
                    $fail('La contraseña actual ingresada es incorrecta.');
                }
            }],
            'password' => [
                'required',
                'string',
                Password::min(8)
                          ->letters() // Opcional: requiere letras
                          ->mixedCase() // Opcional: requiere mayúsculas y minúsculas
                          ->numbers()   // Opcional: requiere números
                          ->symbols()   // Opcional: requiere símbolos
                          ->uncompromised(),
                'confirmed'
            ],
        ],[
            'current_password.required' => 'Debes ingresar tu contraseña actual.',
            'password.required' => 'Debes ingresar una nueva contraseña.',
            'password.min' => 'La nueva contraseña debe tener al menos :min caracteres.',
            'password.confirmed' => 'La confirmación de la nueva contraseña no coincide.',
            'password.uncompromised' => 'Esta contraseña ha sido expuesta en brechas de datos, por favor elige otra.',
        ]);

        // Actualizar la contraseña
        $usuario->forceFill([
            'password' => Hash::make($request->input('password')),
        ])->save();

        // Redirigir de vuelta al perfil con mensaje de éxito
        return redirect()->route('perfil.show')
                         ->with('status', '¡Contraseña actualizada exitosamente!'); // 'status' para el mensaje general
    }

    /**
     * Elimina la cuenta del usuario y todos los datos asociados
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'password_confirmation' => 'required|string',
        ]);

        $user = $request->user();

        // Verificar contraseña
        if (!Hash::check($request->password_confirmation, $user->password)) {
            return back()
                ->withErrors(['password_confirmation' => 'La contraseña proporcionada es incorrecta.'])
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Si el usuario es docente, eliminar sus cursos y materiales
            if ($user->hasRole('docente')) {
                foreach ($user->cursos as $curso) {
                    // Eliminar materiales del curso
                    $curso->materiales()->delete();
                    // Eliminar curso
                    $curso->delete();
                }
            }

            // Si el usuario es estudiante, eliminar sus inscripciones y entregas
            if ($user->hasRole('estudiante')) {
                $user->inscripciones()->delete();
                $user->entregas()->delete();
            }

            // Eliminar foto de perfil si existe
            if ($user->foto) {
                Storage::disk('public')->delete($user->foto);
            }

            // Eliminar usuario
            $user->delete();

            DB::commit();

            // Cerrar sesión
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('home')
                ->with('status', 'Tu cuenta ha sido eliminada permanentemente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar la cuenta de usuario: ' . $e->getMessage());

            return back()
                ->with('error', 'Ha ocurrido un error al eliminar tu cuenta. Por favor, inténtalo de nuevo más tarde.');
        }
    }
}
