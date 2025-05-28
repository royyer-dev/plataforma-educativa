@extends('layouts.app')

@push('styles')
<style>
    /* Estilos base para elementos comunes */
    .container {
        max-width: 1200px;
    }

    /* Animaciones y transiciones */
    .hover-lift {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .hover-lift:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1) !important;
    }

    /* Mejoras visuales para tarjetas */
    .card {
        border: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }
    .card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    /* Estilos para los tabs */
    .nav-tabs {
        border-bottom: 2px solid #e9ecef;
        gap: 0.5rem;
    }
    .nav-tabs .nav-link {
        border: none;
        color: #6c757d;
        padding: 0.75rem 1.25rem;
        font-weight: 500;
        border-radius: 8px 8px 0 0;
        transition: all 0.2s ease;
    }
    .nav-tabs .nav-link:hover {
        background: #f8f9fa;
        color: #0d6efd;
    }
    .nav-tabs .nav-link.active {
        color: #0d6efd;
        background: #f8f9fa;
        position: relative;
    }
    .nav-tabs .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        right: 0;
        height: 2px;
        background: #0d6efd;
    }

    /* Botones y acciones */
    .btn {
        padding: 0.5rem 1rem;
        font-weight: 500;
        border-radius: 6px;
        transition: all 0.2s ease;
    }
    .btn-group-sm .btn {
        padding: 0.25rem 0.75rem;
    }
    .btn i {
        transition: transform 0.2s ease;
    }
    .btn:hover i {
        transform: scale(1.1);
    }

    /* Badges y etiquetas */
    .badge {
        padding: 0.5em 0.8em;
        font-weight: 500;
        border-radius: 6px;
    }

    /* Items de lista */
    .list-group-item {
        border-left: none;
        border-right: none;
        transition: all 0.2s ease;
    }
    .list-group-item:hover {
        background-color: #f8f9fa;
    }
    .list-group-item .actions {
        opacity: 0.7;
        transition: opacity 0.2s ease;
    }
    .list-group-item:hover .actions {
        opacity: 1;
    }

    /* Mejoras en la tipografía */
    h1, h2, h3, h4, h5, h6 {
        color: #2c3e50;
    }
    .text-muted {
        color: #6c757d !important;
    }
    
    /* Contenedor de descripción */
    .description-container {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 1rem;
        margin-top: 1rem;
    }

    /* Iconos en los botones de acción */
    .action-icon {
        width: 20px;
        height: 20px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-right: 0.5rem;
    }
</style>
@endpush

@section('content')
<div class="container py-4"> {{-- Added padding --}}

    {{-- Breadcrumb/Navigation back --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('docente.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('docente.cursos.index') }}">Mis Cursos</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $curso->titulo }}</li>
        </ol>
    </nav>

    {{-- Flash Messages --}}
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif    {{-- Course Header Section --}}
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h1 class="course-title display-5">{{ $curso->titulo }}</h1>
            <p class="text-muted d-flex align-items-center">
                <i class="fas fa-hashtag me-2"></i>
                <span>Código: {{ $curso->codigo_curso ?? 'N/A' }}</span>
            </p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('docente.cursos.edit', $curso->id) }}" 
               class="btn btn-warning btn-lg shadow-sm hover-lift">
                <i class="fas fa-edit me-2"></i>Editar Curso
            </a>
        </div>
    </div>

    {{-- Main Course Info and Image --}}
    <div class="card shadow-sm mb-4">
        <div class="row g-0">
            {{-- Course Image --}}
            @if($curso->ruta_imagen_curso)
                <div class="col-md-4">
                    <img src="{{ Storage::url($curso->ruta_imagen_curso) }}" class="img-fluid rounded-start" alt="Imagen de {{ $curso->titulo }}" style="object-fit: cover; height: 100%; max-height: 300px;">
                </div>
            @else
                {{-- Placeholder if no image --}}
                <div class="col-md-4 bg-light d-flex align-items-center justify-content-center" style="min-height: 200px;">
                    <i class="fas fa-image fa-5x text-secondary opacity-50"></i>
                </div>
            @endif

            {{-- Course Details --}}
            <div class="col-md-8">
                <div class="card-body">
                    <h5 class="card-title mb-3">Detalles del Curso</h5>
                    <ul class="list-group list-group-flush">
                        {{-- vvv CAMPO MODIFICADO: Carrera vvv --}}
                        <li class="list-group-item px-0"><strong>Carrera:</strong> {{ optional($curso->carrera)->nombre ?? 'Sin carrera asignada' }}</li>
                        {{-- ^^^ FIN CAMPO MODIFICADO ^^^ --}}
                        <li class="list-group-item px-0"><strong>Estado:</strong>
                            @if($curso->estado == 'publicado')
                                <span class="badge bg-success">{{ ucfirst($curso->estado) }}</span>
                            @elseif($curso->estado == 'borrador')
                                <span class="badge bg-warning text-dark">{{ ucfirst($curso->estado) }}</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($curso->estado) }}</span>
                            @endif
                        </li>
                        <li class="list-group-item px-0"><strong>Fechas:</strong> {{ $curso->fecha_inicio ? $curso->fecha_inicio->format('d/m/Y') : 'N/A' }} - {{ $curso->fecha_fin ? $curso->fecha_fin->format('d/m/Y') : 'N/A' }}</li>
                        <li class="list-group-item px-0">
                            <strong>Profesor:</strong>
                             @if($curso->profesores && $curso->profesores->count() > 0)
                                {{ $curso->profesores->pluck('nombre')->implode(', ') }}
                            @else
                                No asignado
                            @endif
                        </li>
                    </ul>
                    @if($curso->descripcion)
                        <h6 class="mt-3">Descripción:</h6>
                        <div class="p-2 bg-light rounded border">
                            {!! nl2br(e($curso->descripcion)) !!}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Action Buttons for Content Management --}}
    <div class="mb-4 p-3 bg-light rounded shadow-sm">
        <h5 class="mb-3">Gestionar Contenido del Curso</h5>
        <div class="d-flex flex-wrap gap-2">
             <a href="{{ route('docente.cursos.modulos.create', $curso) }}" class="btn btn-primary">
                 <i class="fas fa-sitemap me-1"></i> Añadir Módulo
             </a>
             <a href="{{ route('docente.cursos.materiales.create', $curso) }}" class="btn btn-success">
                 <i class="fas fa-book-open me-1"></i> Añadir Material
             </a>
             <a href="{{ route('docente.cursos.tareas.create', $curso) }}" class="btn btn-info text-white"> {{-- text-white para mejor contraste en btn-info --}}
                 <i class="fas fa-clipboard-list me-1"></i> Añadir Tarea
             </a>
        </div>
    </div>

    {{-- Sections for Modules, Materials, Tasks --}}
    {{-- (Using tabs for better organization) --}}
    <ul class="nav nav-tabs mb-3" id="courseContentTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="modulos-tab" data-bs-toggle="tab" data-bs-target="#modulos-content" type="button" role="tab" aria-controls="modulos-content" aria-selected="true">
                <i class="fas fa-sitemap me-1"></i> Módulos ({{ $curso->modulos->count() }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="materiales-tab" data-bs-toggle="tab" data-bs-target="#materiales-content" type="button" role="tab" aria-controls="materiales-content" aria-selected="false">
                <i class="fas fa-book-open me-1"></i> Materiales ({{ $curso->materiales->count() }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tareas-tab" data-bs-toggle="tab" data-bs-target="#tareas-content" type="button" role="tab" aria-controls="tareas-content" aria-selected="false">
                <i class="fas fa-clipboard-list me-1"></i> Tareas ({{ $curso->tareas->count() }})
            </button>
        </li>
    </ul>

    <div class="tab-content" id="courseContentTabsContent">
        {{-- Tab Módulos --}}
        <div class="tab-pane fade show active" id="modulos-content" role="tabpanel" aria-labelledby="modulos-tab">
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    @if($curso->modulos && $curso->modulos->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($curso->modulos->sortBy('orden') as $modulo)
                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap gap-2 py-3">
                                    <div>
                                        <h6 class="mb-0"><strong>{{ $modulo->orden ?? $loop->iteration }}. {{ $modulo->titulo }}</strong></h6>
                                        <small class="text-muted">{{ Str::limit($modulo->descripcion, 120) }}</small>
                                    </div>
                                    <div class="text-nowrap">
                                        <a href="{{ route('docente.cursos.modulos.edit', [$curso->id, $modulo->id]) }}" class="btn btn-outline-warning btn-sm"><i class="fas fa-pen me-1"></i>Editar</a>
                                        <form action="{{ route('docente.cursos.modulos.destroy', [$curso->id, $modulo->id]) }}" method="POST" style="display: inline;" onsubmit="return confirm('¿Eliminar este módulo?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm"><i class="fas fa-trash me-1"></i>Eliminar</button>
                                        </form>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted p-3 mb-0">Aún no hay módulos en este curso.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Tab Materiales --}}
        <div class="tab-pane fade" id="materiales-content" role="tabpanel" aria-labelledby="materiales-tab">
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    @if($curso->materiales && $curso->materiales->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($curso->materiales->sortBy(['modulo_id', 'orden', 'titulo']) as $material)
                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap gap-2 py-3">
                                    <div>
                                        <h6 class="mb-0"><strong>{{ $material->titulo }}</strong> <span class="badge bg-secondary ms-1">{{ $material->tipo_material }}</span></h6>
                                        @if($material->modulo)
                                            <span class="badge bg-info ms-1">Módulo: {{ Str::limit(optional($material->modulo)->titulo, 20) }}</span>
                                        @endif
                                        <p class="mb-1 small text-muted">{{ Str::limit($material->descripcion, 100) }}</p>
                                        @if($material->tipo_material == 'enlace' || $material->tipo_material == 'video')
                                            <small><a href="{{ $material->enlace_url }}" target="_blank" rel="noopener noreferrer">Ver enlace</a></small>
                                        @elseif($material->tipo_material == 'archivo' && $material->ruta_archivo)
                                            <small><a href="{{ Storage::url($material->ruta_archivo) }}" target="_blank">Ver/Descargar archivo</a></small>
                                        @endif
                                    </div>
                                    <div class="text-nowrap">
                                        <a href="{{ route('docente.cursos.materiales.edit', [$curso->id, $material->id]) }}" class="btn btn-outline-warning btn-sm"><i class="fas fa-pen me-1"></i>Editar</a>
                                        <form action="{{ route('docente.cursos.materiales.destroy', [$curso->id, $material->id]) }}" method="POST" style="display: inline;" onsubmit="return confirm('¿Eliminar este material?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm"><i class="fas fa-trash me-1"></i>Eliminar</button>
                                        </form>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted p-3 mb-0">Aún no hay materiales en este curso.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Tab Tareas --}}
        <div class="tab-pane fade" id="tareas-content" role="tabpanel" aria-labelledby="tareas-tab">
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    @if($curso->tareas && $curso->tareas->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($curso->tareas->sortBy('fecha_limite') as $tarea)
                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap gap-2 py-3">
                                    <div>
                                        <h6 class="mb-0"><strong>{{ $tarea->titulo }}</strong></h6>
                                        @if($tarea->modulo)
                                            <span class="badge bg-info ms-1">Módulo: {{ Str::limit(optional($tarea->modulo)->titulo, 20) }}</span>
                                        @endif
                                        <p class="mb-1 small text-muted">
                                            Entrega: {{ ucfirst($tarea->tipo_entrega) }} |
                                            Límite: {{ $tarea->fecha_limite ? $tarea->fecha_limite->format('d/m/Y H:i') : 'Sin límite' }}
                                            {{ $tarea->permite_entrega_tardia ? '(Tardía)' : '' }} |
                                            Puntos: {{ $tarea->puntos_maximos ?? 'N/A' }}
                                        </p>
                                    </div>
                                    <div class="text-nowrap">
                                        <a href="{{ route('docente.cursos.tareas.entregas.index', [$curso->id, $tarea->id]) }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-list-check me-1"></i>Ver Entregas</a>
                                        <a href="{{ route('docente.cursos.tareas.edit', [$curso->id, $tarea->id]) }}" class="btn btn-outline-warning btn-sm"><i class="fas fa-pen me-1"></i>Editar</a>
                                        <form action="{{ route('docente.cursos.tareas.destroy', [$curso->id, $tarea->id]) }}" method="POST" style="display: inline;" onsubmit="return confirm('¿Eliminar esta tarea?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm"><i class="fas fa-trash me-1"></i>Eliminar</button>
                                        </form>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted p-3 mb-0">Aún no hay tareas en este curso.</p>
                    @endif
                </div>
            </div>
        </div>
    </div> {{-- Fin tab-content --}}

</div> {{-- Fin container --}}
@endsection
