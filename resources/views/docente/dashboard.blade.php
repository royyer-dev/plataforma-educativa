@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Panel del Docente</h1>

    {{-- Mensajes Flash --}}
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    {{-- ... otros mensajes flash si los necesitas ... --}}

    <div class="row">
        {{-- Columna Mis Cursos Recientes/Activos --}}
        <div class="col-md-8 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-chalkboard me-1"></i> Mis Cursos Recientes</span>
                    <a href="{{ route('docente.cursos.index') }}" class="btn btn-sm btn-outline-secondary">Ver Todos</a>
                </div>
                <div class="card-body">
                    @if($cursosDocente && $cursosDocente->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($cursosDocente as $curso)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <a href="{{ route('docente.cursos.show', $curso->id) }}" class="text-decoration-none">{{ $curso->titulo }}</a>
                                    <span class="badge bg-light text-dark">{{ ucfirst($curso->estado) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">Aún no has creado ningún curso.</p>
                        <a href="{{ route('docente.cursos.create') }}" class="btn btn-primary">Crear mi primer curso</a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Columna Solicitudes Pendientes --}}
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                 <div class="card-header">
                    <i class="fas fa-user-check me-1"></i> Solicitudes Pendientes
                </div>
                <div class="card-body text-center">
                    @if($solicitudesPendientesCount > 0)
                        <p class="fs-1 fw-bold text-warning">{{ $solicitudesPendientesCount }}</p>
                        <p class="mb-2">Solicitud(es) de inscripción esperando tu aprobación.</p>
                        <a href="{{ route('docente.solicitudes.index') }}" class="btn btn-warning">Gestionar Solicitudes</a>
                    @else
                         <p class="text-muted mt-3">No tienes solicitudes pendientes.</p>
                    @endif
                </div>
            </div>
        </div>
    </div> {{-- Fin row --}}

    {{-- Aquí podrías añadir más secciones: Últimas entregas, Tareas próximas a vencer, etc. --}}
    {{-- <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">Actividad Reciente</div>
                <div class="card-body">
                    (Contenido de actividad...)
                </div>
            </div>
        </div>
    </div> --}}

</div>
@endsection
