@extends('layouts.app') {{-- Usa el layout principal --}}

@section('content')
<div class="container">
    {{-- Botón para Volver al Listado de Cursos del Alumno --}}
    <div class="mb-3">
        <a href="{{ route('alumno.cursos.index') }}" class="btn btn-secondary btn-sm">&laquo; Volver a Cursos Disponibles</a>
    </div>

    {{-- Título e Información Básica del Curso --}}
    <div class="card mb-4">
        <div class="card-body">
            <h1 class="card-title">{{ $curso->titulo }}</h1>
            <h6 class="card-subtitle mb-2 text-muted">
                Profesor(es):
                @if($curso->profesores && $curso->profesores->count() > 0)
                    {{ $curso->profesores->pluck('nombre')->implode(', ') }}
                @else
                    No asignado
                @endif
            </h6>
            @if($curso->descripcion)
                <p class="card-text mt-3">{!! nl2br(e($curso->descripcion)) !!}</p>
            @endif
             {{-- Puedes añadir más info si quieres: categoría, fechas, etc. --}}
        </div>
    </div>

    {{-- Contenido del Curso: Módulos, Materiales, Tareas --}}
    {{-- (Secciones de Módulos, Materiales, Tareas - Sin cambios respecto a la versión anterior) --}}

    {{-- Primero, mostrar materiales/tareas generales (sin módulo) si existen --}}
    @php
        $materialesGenerales = $curso->materiales->whereNull('modulo_id');
        $tareasGenerales = $curso->tareas->whereNull('modulo_id');
    @endphp

    @if($materialesGenerales->isNotEmpty() || $tareasGenerales->isNotEmpty())
        <div class="card mb-4">
            <div class="card-header">
                <h5>Contenido General del Curso</h5>
            </div>
            <div class="card-body">
                @if($materialesGenerales->isNotEmpty())
                    <h6>Materiales Generales</h6>
                    <ul class="list-unstyled">
                        @foreach($materialesGenerales->sortBy('orden') as $material)
                            <li>@include('alumno.cursos._material_item', ['material' => $material, 'curso' => $curso])</li>
                        @endforeach
                    </ul>
                    @if($tareasGenerales->isNotEmpty()) <hr> @endif
                @endif
                 @if($tareasGenerales->isNotEmpty())
                    <h6>Tareas Generales</h6>
                    <ul class="list-unstyled">
                        @foreach($tareasGenerales->sortBy('fecha_limite') as $tarea)
                            <li>@include('alumno.cursos._tarea_item', ['tarea' => $tarea, 'curso' => $curso])</li>
                        @endforeach
                    </ul>
                 @endif
            </div>
        </div>
    @endif

    {{-- Luego, iterar sobre los Módulos --}}
    @if($curso->modulos && $curso->modulos->count() > 0)
        @foreach($curso->modulos->sortBy('orden') as $modulo)
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Módulo {{ $modulo->orden ?? $loop->iteration }}: {{ $modulo->titulo }}</h5>
                </div>
                <div class="card-body">
                    @if($modulo->descripcion)
                        <p>{{ $modulo->descripcion }}</p>
                        <hr>
                    @endif
                    {{-- Listar Materiales dentro del Módulo --}}
                    @if($modulo->materiales && $modulo->materiales->count() > 0)
                        <h6>Materiales del Módulo</h6>
                        <ul class="list-unstyled">
                            @foreach($modulo->materiales->sortBy('orden') as $material)
                                <li>@include('alumno.cursos._material_item', ['material' => $material, 'curso' => $curso])</li>
                            @endforeach
                        </ul>
                    @endif
                    {{-- Listar Tareas dentro del Módulo --}}
                    @if($modulo->tareas && $modulo->tareas->count() > 0)
                        <h6 class="mt-3">Tareas del Módulo</h6>
                        <ul class="list-unstyled">
                             @foreach($modulo->tareas->sortBy('fecha_limite') as $tarea)
                                <li>@include('alumno.cursos._tarea_item', ['tarea' => $tarea, 'curso' => $curso])</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        @endforeach
    @else
        @if($materialesGenerales->isEmpty() && $tareasGenerales->isEmpty())
             <div class="alert alert-info">Este curso no tiene contenido publicado todavía.</div>
        @endif
    @endif

    {{-- vvv INICIO: Sección Salir del Curso Modificada vvv --}}
    <hr class="my-4">
    <div class="text-end"> {{-- Alinea el botón a la derecha --}}
        {{-- Botón que abre el Modal --}}
        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalSalirCurso">
            Salir de este Curso
        </button>
    </div>

    {{-- Modal para Confirmar Salida del Curso --}}
    <div class="modal fade" id="modalSalirCurso" tabindex="-1" aria-labelledby="modalSalirCursoLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="modalSalirCursoLabel">Confirmar Salida del Curso</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                {{-- Formulario dentro del modal --}}
                <form id="form-salir-curso" action="{{ route('alumno.cursos.salir', $curso->id) }}" method="POST">
                    @csrf
                    @method('DELETE') {{-- Indica método DELETE --}}
                    <div class="modal-body">
                        <p class="text-danger"><strong>¡Atención!</strong> Si sales de este curso, perderás el acceso a su contenido y tus entregas podrían ser eliminadas. Esta acción no se puede deshacer fácilmente.</p>
                        <div class="mb-3">
                            <label for="password_confirmacion_modal" class="form-label">Confirma tu contraseña para salir <span class="text-danger">*</span></label>
                            {{-- Usamos un ID diferente para el input dentro del modal --}}
                            <input type="password" class="form-control @error('password_confirmacion') is-invalid @enderror" id="password_confirmacion_modal" name="password_confirmacion" required>
                            {{-- Mostrar error específico si la contraseña falla --}}
                            {{-- Nota: Si la validación falla, la página se recargará y el modal no se abrirá automáticamente.
                                 El error se mostrará si lo vuelves a abrir o si lo pones fuera del modal también.
                                 Una solución más avanzada usaría AJAX, pero esto es más simple. --}}
                            @error('password_confirmacion')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Confirmar y Salir</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- ^^^ FIN: Sección Salir del Curso Modificada ^^^ --}}


</div> {{-- Fin container --}}
@endsection
