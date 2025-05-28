@extends('layouts.app')

@push('styles')
<style>
    .hover-shadow {
        transition: box-shadow 0.3s ease-in-out;
    }
    .hover-shadow:hover {
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
    .card {
        border-radius: 0.5rem;
        overflow: hidden;
    }
    .badge {
        padding: 0.4em 0.8em;
        font-weight: 500;
    }
    .list-group-item {
        transition: background-color 0.2s ease;
    }
    .list-group-item:hover {
        background-color: rgba(0,0,0,.01);
    }
    .btn-outline-primary {
        border-width: 1.5px;
    }
    .card-header {
        border-bottom: 1px solid rgba(0,0,0,.05);
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 mb-0 text-gray-800 fw-bold"><i class="fas fa-tachometer-alt me-2 text-primary"></i>Panel del Docente</h1>
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
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Fila de Tarjetas de Estadísticas --}}
    <div class="row mb-4">
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card text-white bg-primary shadow h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fs-4 fw-bold">{{ $totalCursosActivos ?? 0 }}</div>
                            <div>Cursos Activos</div>
                        </div>
                        <i class="fas fa-chalkboard-teacher fa-2x opacity-50"></i>
                    </div>
                </div>
                <a href="{{ route('docente.cursos.index') }}" class="card-footer text-white clearfix small z-1 text-decoration-none">
                    <span class="float-start">Ver Detalles</span>
                    <span class="float-end"><i class="fas fa-angle-right"></i></span>
                </a>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card text-white bg-success shadow h-100">
                <div class="card-body">
                     <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fs-4 fw-bold">{{ $totalEstudiantesInscritos ?? 0 }}</div>
                            <div>Estudiantes Totales</div>
                        </div>
                        <i class="fas fa-users fa-2x opacity-50"></i>
                    </div>
                </div>
                <a href="{{ route('docente.estudiantes.generales') }}" class="card-footer text-white clearfix small z-1 text-decoration-none" title="Ver todos mis estudiantes">
                    <span class="float-start">Ver Detalles</span>
                    <span class="float-end"><i class="fas fa-angle-right"></i></span>
                </a>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card text-white bg-warning shadow h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fs-4 fw-bold">{{ $solicitudesPendientesCount ?? 0 }}</div>
                            <div>Solicitudes Pendientes</div>
                        </div>
                        <i class="fas fa-user-plus fa-2x opacity-50"></i>
                    </div>
                </div>
                <a href="{{ route('docente.solicitudes.index') }}" class="card-footer text-white clearfix small z-1 text-decoration-none">
                    <span class="float-start">Gestionar</span>
                    <span class="float-end"><i class="fas fa-angle-right"></i></span>
                </a>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card text-white bg-danger shadow h-100">
                <div class="card-body">
                     <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fs-4 fw-bold">{{ $totalTareasSinCalificar ?? 0 }}</div>
                            <div>Entregas por Calificar</div>
                        </div>
                        <i class="fas fa-edit fa-2x opacity-50"></i>
                    </div>
                </div>
                <a href="{{ route('docente.entregas.porCalificar') }}" class="card-footer text-white clearfix small z-1 text-decoration-none" title="Ver todas las entregas pendientes">
                    <span class="float-start">Ver Detalles</span>
                    <span class="float-end"><i class="fas fa-angle-right"></i></span>
                </a>
            </div>
        </div>
    </div>    {{-- Fila para Cursos Recientes y Actividad Reciente --}}
    <div class="row">
        <div class="col-lg-7 mb-4">
            <div class="card border-0 h-100 shadow-sm hover-shadow">
                <div class="card-header bg-white border-bottom border-light d-flex justify-content-between align-items-center py-3">
                    <span class="fw-bold text-primary"><i class="fas fa-list-alt me-2"></i>Mis Cursos (Últimos 5)</span>
                    <a href="{{ route('docente.cursos.index') }}" class="btn btn-sm btn-outline-primary">Ver Todos</a>
                </div>
                <div class="card-body">
                    @if($cursosDocente && $cursosDocente->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($cursosDocente->take(5) as $curso)                                <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
                                    <div>
                                        <a href="{{ route('docente.cursos.show', $curso->id) }}" class="text-decoration-none fw-bold text-primary">{{ $curso->titulo }}</a>
                                        <br>
                                        <small class="text-muted">
                                            <span class="badge {{ $curso->estado === 'publicado' ? 'bg-success' : 'bg-warning' }} rounded-pill me-2">
                                                {{ ucfirst($curso->estado) }}
                                            </span>
                                            <i class="fas fa-users me-1"></i> {{ $curso->estudiantes_activos_count }} estudiantes
                                        </small>
                                    </div>
                                    <a href="{{ route('docente.cursos.show', $curso->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-cog me-1"></i>Gestionar
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">Aún no has creado ningún curso.</p>
                    @endif
                </div>
            </div>
        </div>        <div class="col-lg-5 mb-4">
            <div class="card border-0 h-100 shadow-sm hover-shadow">
                <div class="card-header bg-white border-bottom border-light py-3">
                    <span class="fw-bold text-primary"><i class="fas fa-history me-2"></i>Actividad Reciente</span>
                </div>
                <div class="card-body">
                    @if($ultimasEntregas && $ultimasEntregas->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($ultimasEntregas as $entrega)
                                <li class="list-group-item px-0 py-3">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">
                                            <a href="{{ route('docente.cursos.tareas.entregas.calificar.form', [optional($entrega->tarea->curso)->id, optional($entrega->tarea)->id, $entrega->id]) }}" class="text-decoration-none">
                                                {{ Str::limit(optional($entrega->tarea)->titulo, 35) }}
                                            </a>
                                        </h6>
                                        <small class="text-muted">{{ $entrega->created_at->diffForHumans() }}</small>
                                    </div>
                                    <small class="text-muted">
                                        Estudiante: {{ optional($entrega->estudiante)->nombre }} {{ optional($entrega->estudiante)->apellidos }}<br>
                                        Curso: {{ Str::limit(optional(optional($entrega->tarea)->curso)->titulo, 30) }}
                                    </small>
                                </li>
                            @endforeach
                        </ul>
                    @else
                         <p class="text-muted">No hay entregas recientes.</p>
                    @endif
                </div>
            </div>
        </div>
    </div></div>
@endsection
