@extends('layouts.app')

@section('content')
<div class="container">
    {{-- Botón para Volver al Listado --}}
    <div class="mb-3">
        <a href="{{ route('docente.cursos.index') }}" class="btn btn-secondary btn-sm">&laquo; Volver a Mis Cursos</a>
    </div>

    {{-- Título y Botón de Editar Curso --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>{{ $curso->titulo }}</h1>
        <div>
            <a href="{{ route('docente.cursos.edit', $curso->id) }}" class="btn btn-warning">Editar Curso</a>
        </div>
    </div>

    {{-- Mensaje de status --}}
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Detalles del Curso --}}
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Detalles del Curso</h5>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>Código:</strong> {{ $curso->codigo_curso ?? 'N/A' }}</li>
                <li class="list-group-item"><strong>Categoría:</strong> {{ optional($curso->categoria)->nombre ?? 'Sin categoría' }}</li>
                <li class="list-group-item"><strong>Estado:</strong> {{ ucfirst($curso->estado) }}</li>
                <li class="list-group-item"><strong>Fechas:</strong> {{ $curso->fecha_inicio ? $curso->fecha_inicio->format('d/m/Y') : 'N/A' }} - {{ $curso->fecha_fin ? $curso->fecha_fin->format('d/m/Y') : 'N/A' }}</li>
                <li class="list-group-item">
                    <strong>Profesor(es):</strong>
                     @if($curso->profesores && $curso->profesores->count() > 0)
                        {{ $curso->profesores->pluck('nombre')->implode(', ') }}
                    @else
                        No asignado
                    @endif
                </li>
                <li class="list-group-item"><strong>Descripción:</strong></li>
            </ul>
            <div class="p-3 border-top">
                 {!! nl2br(e($curso->descripcion ?? 'Sin descripción.')) !!}
            </div>
        </div>
    </div>

    {{-- Botones de Acción Principales para Contenido --}}
    <div class="mb-4 d-flex flex-wrap gap-2">
         <a href="{{ route('docente.cursos.modulos.create', $curso) }}" class="btn btn-primary">
             <i class="fas fa-plus me-1"></i> Añadir Módulo
         </a>
         <a href="{{ route('docente.cursos.materiales.create', $curso) }}" class="btn btn-success">
             <i class="fas fa-plus me-1"></i> Añadir Material
         </a>
         <a href="{{ route('docente.cursos.tareas.create', $curso) }}" class="btn btn-info">
             <i class="fas fa-plus me-1"></i> Añadir Tarea
         </a>
    </div>


    {{-- Sección de Módulos --}}
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5>Módulos / Unidades</h5>
        </div>
        <div class="card-body p-0">
            @if($curso->modulos && $curso->modulos->count() > 0)
                <ul class="list-group list-group-flush">
                    @foreach($curso->modulos->sortBy('orden') as $modulo)
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <strong>{{ $modulo->orden ?? $loop->iteration }}. {{ $modulo->titulo }}</strong>
                                <p class="mb-0 small text-muted">{{ Str::limit($modulo->descripcion, 100) }}</p>
                            </div>
                            <div class="text-nowrap">
                                <a href="{{ route('docente.cursos.modulos.edit', [$curso->id, $modulo->id]) }}" class="btn btn-warning btn-sm">Editar</a>
                                <form action="{{ route('docente.cursos.modulos.destroy', [$curso->id, $modulo->id]) }}" method="POST" style="display: inline;" onsubmit="return confirm('¿Eliminar este módulo?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
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
    {{-- Fin Sección de Módulos --}}


    {{-- Sección de Materiales --}}
    <div class="card mt-4 mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5>Materiales del Curso</h5>
        </div>
        <div class="card-body p-0">
            {{-- Cargar relación en controlador: $curso->load('materiales.modulo'); --}}
            @if($curso->materiales && $curso->materiales->count() > 0)
                <ul class="list-group list-group-flush">
                    @foreach($curso->materiales->sortBy(['modulo_id', 'orden', 'titulo']) as $material)
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <strong>{{ $material->titulo }}</strong>
                                <span class="badge bg-secondary ms-1">{{ $material->tipo_material }}</span>
                                @if($material->modulo)
                                    <span class="badge bg-info ms-1">M: {{ Str::limit(optional($material->modulo)->titulo, 15) }}</span>
                                @endif
                                <p class="mb-0 small text-muted">{{ Str::limit($material->descripcion, 100) }}</p>

                                {{-- Enlace relevante según tipo --}}
                                @if($material->tipo_material == 'enlace' || $material->tipo_material == 'video')
                                    <p class="mb-0 small"><a href="{{ $material->enlace_url }}" target="_blank" rel="noopener noreferrer">Ver enlace</a></p>
                                @elseif($material->tipo_material == 'archivo' && $material->ruta_archivo)
                                     <p class="mb-0 small"><a href="{{ Storage::url($material->ruta_archivo) }}" target="_blank">Ver/Descargar archivo</a></p>
                                @endif
                            </div>
                            <div class="text-nowrap">
                                <a href="{{ route('docente.cursos.materiales.edit', [$curso->id, $material->id]) }}" class="btn btn-warning btn-sm">Editar</a>
                                <form action="{{ route('docente.cursos.materiales.destroy', [$curso->id, $material->id]) }}" method="POST" style="display: inline;" onsubmit="return confirm('¿Eliminar este material?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
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
    {{-- Fin Sección de Materiales --}}


    {{-- Sección de Tareas --}}
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5>Tareas / Actividades</h5>
        </div>
        <div class="card-body p-0">
            {{-- Cargar relación en controlador: $curso->load('tareas.modulo'); --}}
            @if($curso->tareas && $curso->tareas->count() > 0)
                <ul class="list-group list-group-flush">
                    @foreach($curso->tareas->sortBy('fecha_limite') as $tarea)
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <strong>{{ $tarea->titulo }}</strong>
                                @if($tarea->modulo)
                                    <span class="badge bg-info ms-1">M: {{ Str::limit(optional($tarea->modulo)->titulo, 15) }}</span>
                                @endif
                                <p class="mb-0 small text-muted">
                                    Entrega: {{ ucfirst($tarea->tipo_entrega) }} |
                                    Límite: {{ $tarea->fecha_limite ? $tarea->fecha_limite->format('d/m/Y H:i') : 'Sin límite' }}
                                    {{ $tarea->permite_entrega_tardia ? '(Permite tardía)' : '' }} |
                                    Puntos: {{ $tarea->puntos_maximos ?? 'N/A' }}
                                </p>
                            </div>
                            {{-- Botones de Acción de la Tarea --}}
                            <div class="text-nowrap">
                                {{-- vvv Enlace "Ver Entregas" Añadido/Actualizado vvv --}}
                                <a href="{{ route('docente.cursos.tareas.entregas.index', [$curso->id, $tarea->id]) }}" class="btn btn-secondary btn-sm">Ver Entregas</a>
                                {{-- ^^^ Fin Enlace ^^^ --}}

                                {{-- Botón Editar Tarea --}}
                                <a href="{{ route('docente.cursos.tareas.edit', [$curso->id, $tarea->id]) }}" class="btn btn-warning btn-sm">Editar</a>

                                {{-- Formulario para Eliminar Tarea --}}
                                <form action="{{ route('docente.cursos.tareas.destroy', [$curso->id, $tarea->id]) }}" method="POST" style="display: inline;" onsubmit="return confirm('¿Estás seguro de querer eliminar esta tarea?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
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
    {{-- Fin Sección de Tareas --}}


</div> {{-- Fin container --}}
@endsection
