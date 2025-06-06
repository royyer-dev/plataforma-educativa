<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Curso;
use App\Models\Modulo; // Para listar módulos en los formularios
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // Para manejo de archivos
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class MaterialController extends Controller
{
    /**
     * Muestra el formulario para crear un nuevo material para un curso específico.
     * Ruta: GET /docente/cursos/{curso}/materiales/create
     */
    public function create(Curso $curso): View
    {
        $this->authorizeTeacherAccess($curso); // Verifica permiso sobre el curso

        // Obtener los módulos de este curso para el <select>
        $modulos = $curso->modulos()->orderBy('orden')->pluck('titulo', 'id');

        // Pasar el curso y los módulos a la vista
        return view('docente.materiales.create', compact('curso', 'modulos'));
    }

    /**
     * Almacena un nuevo material para el curso especificado.
     * Ruta: POST /docente/cursos/{curso}/materiales
     */
    public function store(Request $request, Curso $curso): RedirectResponse
    {
        $this->authorizeTeacherAccess($curso);
        
        $rules = [
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'modulo_id' => 'nullable|integer|exists:modulos,id,curso_id,' . $curso->id,
            'tipo_material' => 'required|in:archivo,enlace,texto,video',
        ];

        switch ($request->tipo_material) {
            case 'archivo':
                $rules['ruta_archivo'] = 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,jpg,jpeg,png,gif|max:10240';
                break;
            case 'enlace':
            case 'video':
                $rules['enlace_url'] = 'required|url|max:2048';
                break;
            case 'texto':
                $rules['contenido_texto'] = 'required|string';
                break;
        }

        $validatedData = $request->validate($rules);
        
        try {
            $validatedData['curso_id'] = $curso->id;
            $validatedData['creado_por'] = Auth::id();
            $validatedData['modulo_id'] = $request->input('modulo_id') ?: null;

            if ($request->hasFile('ruta_archivo') && $validatedData['tipo_material'] === 'archivo') {
                $file = $request->file('ruta_archivo');
                
                // Generar un nombre único para el archivo
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                
                // Asegurarse de que el directorio existe
                $directory = "cursos/{$curso->id}/materiales";
                Storage::disk('public')->makeDirectory($directory);
                
                // Guardar el archivo usando storeAs para tener control sobre el nombre
                $path = $file->storeAs($directory, $fileName, 'public');
                
                if (!$path) {
                    throw new \Exception('No se pudo guardar el archivo.');
                }
                
                $validatedData['ruta_archivo'] = $path;
            }

            // Limpiar campos no utilizados según el tipo
            if ($validatedData['tipo_material'] !== 'enlace' && $validatedData['tipo_material'] !== 'video') {
                $validatedData['enlace_url'] = null;
            }
            if ($validatedData['tipo_material'] !== 'texto') {
                $validatedData['contenido_texto'] = null;
            }

            Material::create($validatedData);

            return redirect()->route('docente.cursos.show', $curso->id)
                            ->with('status', '¡Material añadido exitosamente!');
                            
        } catch (\Exception $e) {
            // Si algo sale mal y se subió un archivo, eliminarlo
            if (isset($path)) {
                Storage::disk('public')->delete($path);
            }
            
            return redirect()->back()
                            ->withInput()
                            ->with('error', 'Error al crear el material: ' . $e->getMessage());
        }
    }

    /**
     * Muestra el formulario para editar un material existente.
     * Ruta: GET /docente/cursos/{curso}/materiales/{material}/edit
     */
    public function edit(Curso $curso, Material $material): View
    {
        $this->authorizeTeacherAccess($curso); // Verifica permiso sobre el curso
        $this->ensureMaterialBelongsToCourse($curso, $material); // Verifica que el material sea de este curso

        // Obtener los módulos de este curso para el <select>
        $modulos = $curso->modulos()->orderBy('orden')->pluck('titulo', 'id');

        return view('docente.materiales.edit', compact('curso', 'material', 'modulos'));
    }

    /**
     * Actualiza un material existente en la base de datos.
     * Ruta: PUT/PATCH /docente/cursos/{curso}/materiales/{material}
     */
    public function update(Request $request, Curso $curso, Material $material): RedirectResponse
    {
        $this->authorizeTeacherAccess($curso); // Verifica permiso
        $this->ensureMaterialBelongsToCourse($curso, $material); // Verifica pertenencia

        // Validación (Mejor práctica: Mover a Form Request - UpdateMaterialRequest)
        $rules = [
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'modulo_id' => 'nullable|integer|exists:modulos,id,curso_id,' . $curso->id,
            'tipo_material' => 'required|in:archivo,enlace,texto,video',
        ];

        // Add conditional validation rules based on material type
        switch ($request->tipo_material) {
            case 'archivo':
                // Make file optional on update
                $rules['ruta_archivo'] = 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,jpg,jpeg,png,gif|max:10240';
                break;
            case 'enlace':
            case 'video':
                $rules['enlace_url'] = 'required|url|max:2048';
                break;
            case 'texto':
                $rules['contenido_texto'] = 'required|string';
                break;
        }

        $validatedData = $request->validate($rules);

        $validatedData['modulo_id'] = $request->input('modulo_id') ?: null;

        // Manejo de Actualización de Archivo (si se sube uno nuevo)
        if ($request->hasFile('ruta_archivo') && $validatedData['tipo_material'] === 'archivo') {
            // 1. Borrar archivo antiguo si existe
            if ($material->ruta_archivo) {
                Storage::disk('public')->delete($material->ruta_archivo);
            }
            // 2. Guardar archivo nuevo
            $path = $request->file('ruta_archivo')->store("cursos/{$curso->id}/materiales", 'public');
            $validatedData['ruta_archivo'] = $path;
        } else {
            // Si no se sube archivo nuevo, no queremos cambiar el campo 'ruta_archivo'
             unset($validatedData['ruta_archivo']);
        }

        // Limpiar campos no relevantes para el tipo seleccionado
        if ($validatedData['tipo_material'] !== 'archivo' && $material->ruta_archivo){
             // Si cambiamos de archivo a otro tipo, borramos el archivo físico anterior
             Storage::disk('public')->delete($material->ruta_archivo);
             $validatedData['ruta_archivo'] = null; // Y ponemos null en la BD
        }
        if ($validatedData['tipo_material'] !== 'enlace' && $validatedData['tipo_material'] !== 'video') {
             $validatedData['enlace_url'] = null;
        }
        if ($validatedData['tipo_material'] !== 'texto') {
            $validatedData['contenido_texto'] = null;
        }


        // Actualizar el material
        $material->update($validatedData);

        return redirect()->route('docente.cursos.show', $curso->id)
                         ->with('status', '¡Material actualizado exitosamente!');
    }

    /**
     * Elimina un material específico del curso.
     * Ruta: DELETE /docente/cursos/{curso}/materiales/{material}
     */
    public function destroy(Curso $curso, Material $material): RedirectResponse
    {
        $this->authorizeTeacherAccess($curso); // Verifica permiso
        $this->ensureMaterialBelongsToCourse($curso, $material); // Verifica pertenencia

        // Si es un archivo, borrarlo del disco antes de borrar el registro
        if ($material->tipo_material === 'archivo' && $material->ruta_archivo) {
             if (Storage::disk('public')->exists($material->ruta_archivo)) {
                  Storage::disk('public')->delete($material->ruta_archivo);
             }
        }

        // Eliminar el registro de la base de datos
        $material->delete();

        return redirect()->route('docente.cursos.show', $curso->id)
                         ->with('status', '¡Material eliminado exitosamente!');
    }

    // --- Métodos Auxiliares (puedes moverlos a un Trait o Policy) ---

    protected function authorizeTeacherAccess(Curso $curso): void
    {
        if (!$curso->profesores()->where('profesor_id', Auth::id())->exists()) {
            abort(403, 'No tienes permiso para gestionar este curso.');
        }
    }

    protected function ensureMaterialBelongsToCourse(Curso $curso, Material $material): void
    {
        if ($material->curso_id !== $curso->id) {
            abort(404); // O 403
        }
    }
}