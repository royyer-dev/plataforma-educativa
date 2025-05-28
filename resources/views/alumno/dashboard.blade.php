@extends('layouts.app')

@section('content')
<div class="container py-4">
    {{-- Cabecera --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>

            <h1 class="h3 mb-0">
                <i class="fas fa-columns text-primary me-2"></i>Panel del Estudiante
            </h1>
        </div>
        <a href="{{ route('alumno.carreras.index') }}" class="btn btn-primary">
            <i class="fas fa-search me-2"></i>Explorar Carreras y Cursos
        </a>
    </div>

    {{-- Mensajes Flash --}}
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Tarjetas de Resumen --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                            <i class="fas fa-book-reader text-primary fa-lg"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Cursos Activos</h6>
                            <h3 class="mb-0">{{ $cursosActivos->count() }}</h3>
                        </div>
                    </div>
                    <a href="{{ route('alumno.carreras.index') }}" class="text-decoration-none stretched-link">
                        Ver todos los cursos <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                            <i class="fas fa-tasks text-success fa-lg"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Tareas Pendientes</h6>
                            <h3 class="mb-0">{{ $proximasTareas->count() }}</h3>
                        </div>
                    </div>
                    <a href="{{ route('alumno.agenda.index') }}" class="text-decoration-none stretched-link">
                        Ver mi agenda <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3">
                            <i class="fas fa-graduation-cap text-info fa-lg"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Tu Progreso</h6>
                            <h3 class="mb-0">
                                <a href="{{ route('alumno.calificaciones.index') }}" class="text-decoration-none">
                                    Ver calificaciones
                                </a>
                            </h3>
                        </div>
                    </div>
                    <a href="{{ route('alumno.calificaciones.index') }}" class="text-decoration-none stretched-link">
                        Ver mis calificaciones <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Cursos Activos --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light d-flex align-items-center">
                    <i class="fas fa-book-reader text-primary me-2"></i>
                    <h5 class="mb-0">Mis Cursos Activos</h5>
                </div>
                <div class="card-body p-0">
                    @if($cursosActivos && $cursosActivos->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($cursosActivos as $curso)
                                <div class="list-group-item px-4 py-3">
                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">
                                                <a href="{{ route('alumno.cursos.show', $curso->id) }}" 
                                                   class="text-decoration-none stretched-link">
                                                    {{ $curso->titulo }}
                                                </a>
                                            </h6>
                                            <p class="mb-0 text-muted small">
                                                <i class="fas fa-user-tie me-1"></i>
                                                {{ $curso->profesores->pluck('nombre')->implode(', ') ?: 'Sin profesor asignado' }}
                                            </p>
                                        </div>
                                        <i class="fas fa-chevron-right text-muted"></i>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-book-open fa-3x text-muted"></i>
                            </div>
                            <p class="text-muted mb-0">No estás inscrito en ningún curso activo.</p>
                        </div>
                    @endif
                </div>
                @if($cursosActivos && $cursosActivos->count() > 0)
                    <div class="card-footer bg-light text-center py-3">
                        <a href="{{ route('alumno.carreras.index') }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-search me-1"></i>Explorar más cursos
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Próximas Tareas --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light d-flex align-items-center">
                    <i class="fas fa-calendar-check text-primary me-2"></i>
                    <h5 class="mb-0">Próximas Entregas</h5>
                </div>
                <div class="card-body p-0">
                    @if($proximasTareas && $proximasTareas->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($proximasTareas as $tarea)
                                <div class="list-group-item px-4 py-3">
                                    <div class="d-flex w-100 justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">
                                                <a href="{{ route('alumno.cursos.tareas.show', [$tarea->curso->id, $tarea->id]) }}" 
                                                   class="text-decoration-none stretched-link">
                                                    {{ $tarea->titulo }}
                                                </a>
                                            </h6>
                                            <p class="mb-0 text-muted small">
                                                <i class="fas fa-book me-1"></i>{{ Str::limit($tarea->curso->titulo, 25) }}
                                            </p>
                                            <p class="mb-0 small">
                                                <i class="fas fa-clock text-warning me-1"></i>
                                                <span class="text-danger">Vence {{ $tarea->fecha_limite ? $tarea->fecha_limite->diffForHumans() : 'Sin fecha' }}</span>
                                            </p>
                                        </div>
                                        <span class="badge bg-warning text-dark rounded-pill">Pendiente</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-check-circle fa-3x text-success"></i>
                            </div>
                            <p class="text-muted mb-0">¡Todo al día! No tienes tareas pendientes.</p>
                        </div>
                    @endif
                </div>
                @if($proximasTareas && $proximasTareas->count() > 0)
                    <div class="card-footer bg-light text-center py-3">
                        <a href="{{ route('alumno.agenda.index') }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-calendar-alt me-1"></i>Ver mi agenda completa
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.card {
    transition: transform 0.2s ease;
}
.card:hover {
    transform: translateY(-2px);
}
.list-group-item {
    transition: background-color 0.2s ease;
}
.list-group-item:hover {
    background-color: rgba(13, 110, 253, 0.025);
}
.badge {
    font-weight: 500;
}
</style>
@endsection
