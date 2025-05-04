<?php

// Namespace correcto para el controlador del Docente
namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\Curso;
use App\Models\Categoria;
use App\Models\Usuario;
use App\Models\Inscripcion;
use App\Models\Modulo;
use App\Models\Tarea;
use App\Models\Entrega;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CursoController extends Controller
{
    /**
     * Muestra una lista de los cursos gestionados por el docente autenticado.
     */
    public function index(): View
    {
        $docente = Auth::user();
        $cursos = $docente->cursosImpartidos()
                          ->orderBy('titulo')
                          ->paginate(10);
        return view('docente.cursos.index', compact('cursos'));
    }

    /**
     * Muestra el formulario para crear un nuevo curso.
     */
    public function create(): View
    {
        $categorias = Categoria::orderBy('nombre')->pluck('nombre', 'id');
        return view('docente.cursos.create', compact('categorias'));
    }

    /**
     * Almacena un nuevo curso creado en la base de datos.
     */
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'titulo' => 'required|string|max:255',
            'codigo_curso' => 'nullable|string|max:20|unique:cursos,codigo_curso',
            'descripcion' => 'nullable|string',
            'categoria_id' => 'nullable|integer|exists:categorias,id',
            'estado' => 'required|in:borrador,publicado,archivado',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
        ]);
        $docente = Auth::user();
        $curso = Curso::create($validatedData);
        $curso->profesores()->attach($docente->id);
        return redirect()->route('docente.cursos.index')
                         ->with('status', '¡Curso creado exitosamente!');
    }

    /**
     * Muestra los detalles de un curso específico (vista del docente).
     */
    public function show(Curso $curso): View
    {
        $this->authorizeTeacherAccess($curso);
        $curso->load(['categoria', 'profesores', 'modulos', 'materiales.modulo', 'tareas.modulo']);
        return view('docente.cursos.show', compact('curso'));
    }

    /**
     * Muestra el formulario para editar un curso existente.
     */
    public function edit(Curso $curso): View
    {
        $this->authorizeTeacherAccess($curso);
        $categorias = Categoria::orderBy('nombre')->pluck('nombre', 'id');
        $estados = ['borrador' => 'Borrador', 'publicado' => 'Publicado', 'archivado' => 'Archivado'];
        return view('docente.cursos.edit', compact('curso', 'categorias', 'estados'));
    }

    /**
     * Actualiza un curso existente en la base de datos.
     */
    public function update(Request $request, Curso $curso): RedirectResponse
    {
        $this->authorizeTeacherAccess($curso);
        $validatedData = $request->validate([
            'titulo' => 'required|string|max:255',
            'codigo_curso' => 'nullable|string|max:20|unique:cursos,codigo_curso,' . $curso->id,
            'descripcion' => 'nullable|string',
            'categoria_id' => 'nullable|integer|exists:categorias,id',
            'estado' => 'required|in:borrador,publicado,archivado',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
        ]);
        $curso->update($validatedData);
        return redirect()->route('docente.cursos.show', $curso->id)
                         ->with('status', '¡Curso actualizado exitosamente!');
    }

    /**
     * Elimina un curso específico de la base de datos.
     */
    public function destroy(Curso $curso): RedirectResponse
    {
        $this->authorizeTeacherAccess($curso);
        $curso->delete();
        return redirect()->route('docente.cursos.index')
                         ->with('status', '¡Curso eliminado exitosamente!');
    }

    /**
     * Muestra la lista de estudiantes inscritos y activos en un curso específico.
     */
    public function verEstudiantes(Curso $curso): View
    {
        $this->authorizeTeacherAccess($curso);
        $estudiantesInscritos = $curso->estudiantes()
                                     ->wherePivot('estado', 'activo')
                                     ->orderBy('apellidos')
                                     ->orderBy('nombre')
                                     ->get();
        // vvv CORRECCIÓN: Usar el nombre de vista correcto sin '.index' vvv
        return view('docente.cursos.estudiantes', compact('curso', 'estudiantesInscritos'));
        // ^^^ FIN CORRECCIÓN ^^^
    }

    /**
     * Muestra detalles y progreso de un estudiante en el curso.
     */
    public function verDetallesEstudiante(Curso $curso, Usuario $estudiante): View | RedirectResponse
    {
        $this->authorizeTeacherAccess($curso);
        $inscripcionActiva = $curso->estudiantes()
                                   ->wherePivot('estado', 'activo')
                                   ->find($estudiante->id);
        if (!$inscripcionActiva) {
            return redirect()->route('docente.cursos.estudiantes.index', $curso->id)
                             ->with('error', 'Este estudiante no está inscrito o activo en el curso.');
        }

        $tareasDelCurso = $curso->tareas()->with('modulo')->orderBy('fecha_limite')->get();
        $tareasIds = $tareasDelCurso->pluck('id');

        $entregasEstudiante = $estudiante->entregasRealizadas()
                                         ->whereIn('tarea_id', $tareasIds)
                                         ->with('tarea')
                                         ->get()
                                         ->keyBy('tarea_id');

        // Calcular Promedios
        $promedioGeneral = 0; $totalPuntosPosiblesGeneral = 0; $totalPuntosObtenidosGeneral = 0;
        $promediosPorModulo = []; $modulosDelCurso = $curso->modulos()->orderBy('orden')->get();
        $entregasCalificadas = $entregasEstudiante->whereNotNull('calificacion');

        foreach ($modulosDelCurso as $modulo) {
            $tareasDelModulo = $tareasDelCurso->where('modulo_id', $modulo->id);
            $totalPuntosPosiblesModulo = 0; $totalPuntosObtenidosModulo = 0;
            foreach ($tareasDelModulo as $tarea) {
                if ($tarea->puntos_maximos !== null && $tarea->puntos_maximos > 0) {
                    $entrega = $entregasCalificadas->get($tarea->id);
                    if ($entrega) {
                        $totalPuntosObtenidosModulo += $entrega->calificacion;
                        $totalPuntosPosiblesModulo += $tarea->puntos_maximos;
                    }
                }
            }
            $promedioModulo = ($totalPuntosPosiblesModulo > 0) ? round(($totalPuntosObtenidosModulo / $totalPuntosPosiblesModulo) * 100, 2) : null;
            $promediosPorModulo[$modulo->id] = ['titulo' => $modulo->titulo, 'promedio' => $promedioModulo, 'obtenidos' => $totalPuntosObtenidosModulo, 'posibles' => $totalPuntosPosiblesModulo];
            $totalPuntosObtenidosGeneral += $totalPuntosObtenidosModulo; $totalPuntosPosiblesGeneral += $totalPuntosPosiblesModulo;
        }

        $tareasSinModulo = $tareasDelCurso->whereNull('modulo_id');
        $totalPuntosPosiblesSinModulo = 0; $totalPuntosObtenidosSinModulo = 0;
        foreach ($tareasSinModulo as $tarea) {
             if ($tarea->puntos_maximos !== null && $tarea->puntos_maximos > 0) {
                 $entrega = $entregasCalificadas->get($tarea->id);
                 if ($entrega) {
                     $totalPuntosObtenidosSinModulo += $entrega->calificacion; $totalPuntosPosiblesSinModulo += $tarea->puntos_maximos;
                 }
             }
        }
        $promedioSinModulo = ($totalPuntosPosiblesSinModulo > 0) ? round(($totalPuntosObtenidosSinModulo / $totalPuntosPosiblesSinModulo) * 100, 2) : null;
        $totalPuntosObtenidosGeneral += $totalPuntosObtenidosSinModulo; $totalPuntosPosiblesGeneral += $totalPuntosPosiblesSinModulo;
        $promedioGeneral = ($totalPuntosPosiblesGeneral > 0) ? round(($totalPuntosObtenidosGeneral / $totalPuntosPosiblesGeneral) * 100, 2) : null;

        return view('docente.cursos.estudiantes.show', compact(
            'curso', 'estudiante', 'tareasDelCurso', 'entregasEstudiante',
            'promediosPorModulo', 'promedioSinModulo', 'promedioGeneral'
        ));
    }

    /**
     * Elimina la inscripción de un estudiante específico de un curso.
     */
    public function darDeBajaEstudiante(Request $request, Curso $curso, Usuario $estudiante): RedirectResponse
    {
        $this->authorizeTeacherAccess($curso);
        $inscripcion = Inscripcion::where('curso_id', $curso->id)
                                  ->where('estudiante_id', $estudiante->id)
                                  ->first();
        if (!$inscripcion) {
            return redirect()->route('docente.cursos.estudiantes.index', $curso->id)
                             ->with('error', 'No se encontró la inscripción de este estudiante en el curso.');
        }
        $inscripcion->delete();
        return redirect()->route('docente.cursos.estudiantes.index', $curso->id)
                         ->with('status', 'Estudiante ' . $estudiante->nombre . ' dado de baja del curso exitosamente.');
    }

    // --- Método auxiliar para autorización ---
    protected function authorizeTeacherAccess(Curso $curso): void
    {
        if (!$curso->profesores()->where('profesor_id', Auth::id())->exists()) {
            abort(403, 'No tienes permiso para acceder o modificar este curso.');
        }
    }
}
