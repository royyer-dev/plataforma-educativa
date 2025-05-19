@extends('layouts.app')

@section('content')
<div class="container py-4"> {{-- Añadido padding general --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-user-graduate me-2"></i>Panel del Estudiante</h1>
        {{-- Podrías añadir un botón de acción principal aquí si fuera necesario --}}
        <a href="{{ route('alumno.carreras.index') }}" class="btn btn-outline-primary">
            <i class="fas fa-search me-1"></i> Explorar Carreras y Cursos
        </a>
    </div>

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

    <div class="row">
        {{-- Columna Cursos Activos --}}
        <div class="col-lg-7 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-book-reader me-2"></i>Mis Cursos Activos</h5>
                </div>
                <div class="card-body">
                    @if($cursosActivos && $cursosActivos->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($cursosActivos as $curso)
                                <li class="list-group-item px-0 py-3">
                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">
                                                <a href="{{ route('alumno.cursos.show', $curso->id) }}" class="fw-bold text-decoration-none text-primary stretched-link">{{ $curso->titulo }}</a>
                                            </h6>
                                            <small class="text-muted">
                                                Profesor(es): {{ $curso->profesores->pluck('nombre')->implode(', ') ?: 'N/A' }}
                                            </small>
                                        </div>
                                        {{-- El enlace es el título ahora, el botón es redundante si solo va al curso --}}
                                        {{-- <a href="{{ route('alumno.cursos.show', $curso->id) }}" class="btn btn-sm btn-outline-primary">Ir al Curso</a> --}}
                                        <i class="fas fa-chevron-right text-muted"></i>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                        {{-- Enlace a ver todas las carreras y cursos --}}
                        @php
                            $totalCursosPublicados = \App\Models\Curso::where('estado','publicado')->count();
                            $totalInscripcionesActivas = Auth::user()->inscripciones()->where('estado', 'activo')->count();
                            // Mostrar si hay más cursos activos de los que se listan O si hay más cursos publicados en general
                            $mostrarEnlaceExplorar = $totalInscripcionesActivas > $cursosActivos->count() || $cursosActivos->count() < $totalCursosPublicados;
                        @endphp
                        @if($mostrarEnlaceExplorar && $cursosActivos->count() < $totalCursosPublicados) {{-- Solo mostrar si realmente hay más para explorar --}}
                         <div class="text-center mt-3 pt-3 border-top">
                             <a href="{{ route('alumno.carreras.index') }}" class="btn btn-sm btn-outline-secondary">Ver más cursos en otras carreras</a>
                         </div>
                        @endif
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-folder-open fa-3x text-muted mb-2"></i>
                            <p class="text-muted">No estás inscrito en ningún curso activo.</p>
                            <a href="{{ route('alumno.carreras.index') }}" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i> Explorar Carreras y Cursos
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Columna Próximas Tareas --}}
        <div class="col-lg-5 mb-4">
            <div class="card h-100 shadow-sm">
                 <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-calendar-check me-2"></i>Próximas Tareas</h5>
                </div>
                <div class="card-body">
                     @if($proximasTareas && $proximasTareas->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($proximasTareas as $tarea)
                                <li class="list-group-item px-0 py-3">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">
                                            <a href="{{ route('alumno.cursos.tareas.show', [$tarea->curso->id, $tarea->id]) }}" class="text-decoration-none stretched-link">{{ $tarea->titulo }}</a>
                                        </h6>
                                        <small class="text-danger fw-bold">{{ $tarea->fecha_limite ? $tarea->fecha_limite->diffForHumans() : '' }}</small>
                                    </div>
                                    <small class="text-muted d-block">
                                        <i class="fas fa-book-open me-1"></i>Curso: {{ Str::limit($tarea->curso->titulo, 25) }}
                                    </small>
                                    <small class="text-muted d-block">
                                        <i class="fas fa-clock me-1"></i>Vence: {{ $tarea->fecha_limite ? $tarea->fecha_limite->format('d/m/Y H:i A') : 'Sin fecha' }}
                                    </small>
                                </li>
                            @endforeach
                        </ul>
                     @else
                         <div class="text-center py-3">
                            <i class="fas fa-check-circle fa-3x text-muted mb-2"></i>
                            <p class="text-muted">¡Todo al día! No tienes tareas próximas.</p>
                        </div>
                     @endif
                </div>
            </div>
        </div>
    </div> {{-- Fin row --}}

    {{-- Sección Adicional Placeholder: Anuncios o Calificaciones Recientes --}}
    <div class="row mt-2">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-bullhorn me-2"></i>Anuncios Recientes</h5>
                </div>
                <div class="card-body text-center py-5">
                    <p class="text-muted">Aquí se mostrarán los anuncios importantes de tus cursos.</p>
                    <i class="fas fa-newspaper fa-4x text-light"></i>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
