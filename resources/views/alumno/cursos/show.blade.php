@extends('layouts.app')

@section('content')
<div class="container py-4">

    {{-- Breadcrumbs para mejor navegación --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('alumno.dashboard') }}">Dashboard</a></li>
            {{-- Asumimos que $curso->carrera existe y está cargado --}}
            @if(optional($curso->carrera)->id)
                <li class="breadcrumb-item"><a href="{{ route('alumno.carreras.index') }}">Carreras</a></li>
                <li class="breadcrumb-item"><a href="{{ route('alumno.cursos.index', ['carrera' => $curso->carrera->id]) }}">{{ $curso->carrera->nombre }}</a></li>
            @else
                 {{-- Fallback si no hay carrera, podría enlazar a una lista general de cursos si existiera --}}
                 <li class="breadcrumb-item"><a href="{{ route('alumno.carreras.index') }}">Cursos</a></li>
            @endif
            <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($curso->titulo, 30) }}</li>
        </ol>
    </nav>

    {{-- Mensajes Flash --}}
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Encabezado del Curso con Imagen --}}
    <div class="card shadow-lg mb-4 overflow-hidden">
        @if($curso->ruta_imagen_curso)
            {{-- vvv ALTURA DE IMAGEN MODIFICADA vvv --}}
            <img src="{{ Storage::url($curso->ruta_imagen_curso) }}" class="card-img-top" alt="Imagen de {{ $curso->titulo }}" style="height: 200px; object-fit: cover;">
            {{-- ^^^ FIN MODIFICACIÓN ^^^ --}}
        @else
            {{-- vvv ALTURA DE PLACEHOLDER MODIFICADA vvv --}}
            <div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 200px;">
            {{-- ^^^ FIN MODIFICACIÓN ^^^ --}}
                <i class="fas fa-chalkboard-teacher fa-5x opacity-50"></i>
            </div>
        @endif
        <div class="card-body">
            <h1 class="card-title display-5">{{ $curso->titulo }}</h1>
            <p class="card-text text-muted">
                <i class="fas fa-graduation-cap me-1"></i> Carrera: {{ optional($curso->carrera)->nombre ?? 'No especificada' }}
            </p>
            <p class="card-text">
                <i class="fas fa-user-tie me-1"></i> Profesor(es):
                @if($curso->profesores && $curso->profesores->count() > 0)
                    {{ $curso->profesores->pluck('nombre')->implode(', ') }}
                @else
                    No asignado
                @endif
            </p>
            @if($curso->descripcion)
                <p class="card-text mt-3">{!! nl2br(e($curso->descripcion)) !!}</p>
            @endif
        </div>
    </div>

    {{-- Contenido del Curso: Módulos, Materiales, Tareas --}}
    <h3 class="mb-3"><i class="fas fa-stream me-2"></i>Contenido del Curso</h3>

    @php
        $materialesGenerales = $curso->materiales->whereNull('modulo_id');
        $tareasGenerales = $curso->tareas->whereNull('modulo_id');
    @endphp

    {{-- Contenido General (si existe) --}}
    @if($materialesGenerales->isNotEmpty() || $tareasGenerales->isNotEmpty())
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fas fa-folder-open me-2"></i>Contenido General del Curso</h5>
            </div>
            <div class="card-body">
                @if($materialesGenerales->isNotEmpty())
                    <h6 class="text-primary"><i class="fas fa-book me-2"></i>Materiales Generales:</h6>
                    <ul class="list-unstyled ps-3">
                        @foreach($materialesGenerales->sortBy('orden') as $material)
                            <li>@include('alumno.cursos._material_item', ['material' => $material, 'curso' => $curso])</li>
                        @endforeach
                    </ul>
                    @if($tareasGenerales->isNotEmpty()) <hr class="my-3"> @endif
                @endif

                 @if($tareasGenerales->isNotEmpty())
                    <h6 class="text-info"><i class="fas fa-clipboard-list me-2"></i>Tareas Generales:</h6>
                    <ul class="list-unstyled ps-3">
                        @foreach($tareasGenerales->sortBy('fecha_limite') as $tarea)
                            <li>@include('alumno.cursos._tarea_item', ['tarea' => $tarea, 'curso' => $curso])</li>
                        @endforeach
                    </ul>
                 @endif
            </div>
        </div>
    @endif

    {{-- Módulos (usando Acordeón de Bootstrap) --}}
    @if($curso->modulos && $curso->modulos->count() > 0)
        <div class="accordion shadow-sm" id="accordionModulos">
            @foreach($curso->modulos->sortBy('orden') as $index => $modulo)
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingModulo{{ $modulo->id }}">
                        <button class="accordion-button {{ $index == 0 ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapseModulo{{ $modulo->id }}" aria-expanded="{{ $index == 0 ? 'true' : 'false' }}" aria-controls="collapseModulo{{ $modulo->id }}">
                            <i class="fas fa-sitemap me-2"></i>
                            <strong>Módulo {{ $modulo->orden ?? $loop->iteration }}: {{ $modulo->titulo }}</strong>
                        </button>
                    </h2>
                    <div id="collapseModulo{{ $modulo->id }}" class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}" aria-labelledby="headingModulo{{ $modulo->id }}" data-bs-parent="#accordionModulos">
                        <div class="accordion-body">
                            @if($modulo->descripcion)
                                <p>{{ $modulo->descripcion }}</p>
                                <hr>
                            @endif

                            @if($modulo->materiales && $modulo->materiales->count() > 0)
                                <h6 class="text-primary"><i class="fas fa-book me-2"></i>Materiales del Módulo:</h6>
                                <ul class="list-unstyled ps-3">
                                    @foreach($modulo->materiales->sortBy('orden') as $material)
                                        <li>@include('alumno.cursos._material_item', ['material' => $material, 'curso' => $curso])</li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-muted small"><em>No hay materiales en este módulo.</em></p>
                            @endif

                            @if($modulo->tareas && $modulo->tareas->count() > 0)
                                <h6 class="mt-3 text-info"><i class="fas fa-clipboard-list me-2"></i>Tareas del Módulo:</h6>
                                <ul class="list-unstyled ps-3">
                                     @foreach($modulo->tareas->sortBy('fecha_limite') as $tarea)
                                        <li>@include('alumno.cursos._tarea_item', ['tarea' => $tarea, 'curso' => $curso])</li>
                                    @endforeach
                                </ul>
                            @else
                                 <p class="text-muted small mt-3"><em>No hay tareas en este módulo.</em></p>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        @if($materialesGenerales->isEmpty() && $tareasGenerales->isEmpty())
             <div class="alert alert-light text-center shadow-sm" role="alert">
                <i class="fas fa-info-circle fa-2x mb-2 d-block text-primary"></i>
                Este curso no tiene contenido publicado todavía.
            </div>
        @endif
    @endif

    {{-- Sección Salir del Curso (Modal) --}}
    <hr class="my-5"> {{-- Más separación --}}
    <div class="text-center mb-4"> {{-- Centrar botón --}}
        <button type="button" class="btn btn-outline-danger btn-lg" data-bs-toggle="modal" data-bs-target="#modalSalirCurso">
            <i class="fas fa-sign-out-alt me-2"></i>Salir de este Curso
        </button>
    </div>
    <div class="modal fade" id="modalSalirCurso" tabindex="-1" aria-labelledby="modalSalirCursoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"> {{-- Centrar modal --}}
            <div class="modal-content shadow-lg">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="modalSalirCursoLabel"><i class="fas fa-exclamation-triangle me-2"></i>Confirmar Salida del Curso</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form-salir-curso" action="{{ route('alumno.cursos.salir', $curso->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <p>Si sales de este curso, perderás el acceso a su contenido y tus entregas podrían ser eliminadas. Esta acción no se puede deshacer fácilmente.</p>
                        <p class="fw-bold">¿Estás seguro de querer continuar?</p>
                        <div class="mb-3 mt-3">
                            <label for="password_confirmacion_modal" class="form-label">Confirma tu contraseña para salir <span class="text-danger">*</span></label>
                            <input type="password" class="form-control @error('password_confirmacion') is-invalid @enderror" id="password_confirmacion_modal" name="password_confirmacion" required>
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

</div> {{-- Fin container --}}
@endsection
