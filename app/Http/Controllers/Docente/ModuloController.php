<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\Modulo;
use App\Models\Curso; // Necesario para recibir el curso padre
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Para verificar el usuario autenticado
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse; // Para el tipo de retorno en redirecciones

class ModuloController extends Controller
{
    /**
     * Constructor opcional para aplicar middleware a todo el controlador si fuera necesario.
     * Por ejemplo, asegurar que solo docentes accedan a CUALQUIER método aquí.
     * Aunque ya lo hacemos en las rutas, es una capa extra de seguridad.
     */
    // public function __construct()
    // {
    //     $this->middleware(['auth', 'role:docente']);
    // }

    /**
     * Muestra el formulario para crear un nuevo módulo para un curso específico.
     * Ruta: GET /docente/cursos/{curso}/modulos/create
     * Nombre: docente.cursos.modulos.create
     */
    public function create(Curso $curso): View
    {
        // Verifica si el docente autenticado puede gestionar este curso (simple check)
        $this->authorizeTeacherAccess($curso);

        // Pasa el curso a la vista para saber a qué curso añadir el módulo
        return view('docente.modulos.create', compact('curso'));
    }

    /**
     * Almacena un nuevo módulo para el curso especificado.
     * Ruta: POST /docente/cursos/{curso}/modulos
     * Nombre: docente.cursos.modulos.store
     */
    public function store(Request $request, Curso $curso): RedirectResponse
    {
        // Verifica si el docente autenticado puede gestionar este curso
        $this->authorizeTeacherAccess($curso);

        // Validación (mover a Form Request: StoreModuloRequest es mejor práctica)
        $validatedData = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'orden' => 'nullable|integer|min:0',
        ]);

        // Crear el módulo asociado directamente al curso usando la relación
        $curso->modulos()->create($validatedData);

        // Redirigir de vuelta a la vista del curso con mensaje de éxito
        return redirect()->route('docente.cursos.show', $curso->id)
                         ->with('status', '¡Módulo añadido exitosamente!');
    }

    /**
     * Muestra el formulario para editar un módulo existente.
     * Ruta: GET /docente/cursos/{curso}/modulos/{modulo}/edit
     * Nombre: docente.cursos.modulos.edit
     * Laravel inyecta Curso y Modulo gracias al Route Model Binding.
     */
    public function edit(Curso $curso, Modulo $modulo): View
    {
        // Verifica si el docente puede gestionar este curso Y si el módulo pertenece al curso
        $this->authorizeTeacherAccess($curso);
        $this->ensureModuleBelongsToCourse($curso, $modulo);

        // Retorna la vista de edición, pasando el curso y el módulo
        return view('docente.modulos.edit', compact('curso', 'modulo'));
    }

    /**
     * Actualiza un módulo existente en la base de datos.
     * Ruta: PUT/PATCH /docente/cursos/{curso}/modulos/{modulo}
     * Nombre: docente.cursos.modulos.update
     */
    public function update(Request $request, Curso $curso, Modulo $modulo): RedirectResponse
    {
        // Verifica si el docente puede gestionar este curso Y si el módulo pertenece al curso
        $this->authorizeTeacherAccess($curso);
        $this->ensureModuleBelongsToCourse($curso, $modulo);

        // Validación (mover a Form Request: UpdateModuloRequest es mejor práctica)
        $validatedData = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'orden' => 'nullable|integer|min:0',
        ]);

        // Actualiza el módulo con los datos validados
        $modulo->update($validatedData);

        // Redirige de vuelta a la vista del curso con mensaje de éxito
        return redirect()->route('docente.cursos.show', $curso->id)
                         ->with('status', '¡Módulo actualizado exitosamente!');
    }

    /**
     * Elimina un módulo específico del curso.
     * Ruta: DELETE /docente/cursos/{curso}/modulos/{modulo}
     * Nombre: docente.cursos.modulos.destroy
     */
    public function destroy(Curso $curso, Modulo $modulo): RedirectResponse
    {
         // Verifica si el docente puede gestionar este curso Y si el módulo pertenece al curso
        $this->authorizeTeacherAccess($curso);
        $this->ensureModuleBelongsToCourse($curso, $modulo);

        // Elimina el módulo
        // Si tienes materiales/tareas asociados y quieres que se borren en cascada,
        // asegúrate que las claves foráneas en sus migraciones tengan onDelete('cascade')
        $modulo->delete();

        // Redirige de vuelta a la vista del curso con mensaje de éxito
        return redirect()->route('docente.cursos.show', $curso->id)
                         ->with('status', '¡Módulo eliminado exitosamente!');
    }

    // --- Métodos Auxiliares ---

    /**
     * Verifica si el usuario autenticado es profesor de este curso.
     * Lanza una excepción 403 si no lo es.
     */
    protected function authorizeTeacherAccess(Curso $curso): void
    {
        // Asume que Auth::user() es el docente. Verifica si está en la lista de profesores del curso.
        if (!$curso->profesores()->where('profesor_id', Auth::id())->exists()) {
            abort(403, 'No tienes permiso para gestionar este curso.');
        }
        // Podrías usar Policies para una lógica más compleja:
        // Gate::authorize('manage-curso', $curso);
    }

     /**
     * Verifica si un módulo pertenece realmente al curso especificado.
     * Lanza una excepción 404 si no pertenece (o 403 si prefieres).
     */
    protected function ensureModuleBelongsToCourse(Curso $curso, Modulo $modulo): void
    {
        if ($modulo->curso_id !== $curso->id) {
            abort(404); // O abort(403, 'Este módulo no pertenece a este curso.');
        }
    }
}