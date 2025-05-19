@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Panel del Docente</h1>
        {{-- Botón para crear curso directamente desde el dashboard --}}
        <a href="{{ route('docente.cursos.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Crear Nuevo Curso
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

    {{-- Fila de Tarjetas de Estadísticas --}}
    <div class="row mb-4">
        {{-- Tarjeta Cursos Activos --}}
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
        {{-- Tarjeta Estudiantes Inscritos --}}
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card text-white bg-success shadow h-100">
                <div class="card-body">
                     <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fs-4 fw-bold">{{ $totalEstudiantesInscritos ?? 0 }}</div>
                            <div>Estudiantes Totales</div> {{-- (Activos en sus cursos) --}}
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
        {{-- Tarjeta Solicitudes Pendientes --}}
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
        {{-- Tarjeta Entregas por Calificar --}}
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
                </a>
            </div>
        </div>
    </div>


    <div class="row">
        {{-- Columna Mis Cursos Recientes/Activos --}}
        <div class="col-lg-7 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-list-alt me-1"></i> Mis Cursos (Últimos 5 o Activos)</span>
                    <a href="{{ route('docente.cursos.index') }}" class="btn btn-sm btn-outline-secondary">Ver Todos</a>
                </div>
                <div class="card-body">
                    @if($cursosDocente && $cursosDocente->count() > 0)
                        <ul class="list-group list-group-flush">
                            {{-- Mostramos solo los primeros 5 o los que haya si son menos --}}
                            @foreach($cursosDocente->take(5) as $curso)
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
                                    <div>
                                        <a href="{{ route('docente.cursos.show', $curso->id) }}" class="text-decoration-none fw-bold">{{ $curso->titulo }}</a>
                                        <br>
                                        <small class="text-muted">Estado: {{ ucfirst($curso->estado) }} | Estudiantes: {{ $curso->estudiantes()->wherePivot('estado', 'activo')->count() }}</small>
                                    </div>
                                    <a href="{{ route('docente.cursos.show', $curso->id) }}" class="btn btn-sm btn-outline-primary">Gestionar</a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">Aún no has creado ningún curso.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Columna Actividad Reciente (Últimas Entregas) --}}
        <div class="col-lg-5 mb-4">
            <div class="card h-100 shadow-sm">
                 <div class="card-header">
                    <i class="fas fa-history me-1"></i> Actividad Reciente (Últimas Entregas)
                </div>
                <div class="card-body">
                    @if($ultimasEntregas && $ultimasEntregas->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($ultimasEntregas as $entrega)
                                <li class="list-group-item px-0 py-3">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">
                                            {{-- Enlace a la página para calificar esa entrega específica --}}
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
    </div>

    {{-- Placeholder para Gráfica --}}
    <div class="row mt-2">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header"><i class="fas fa-chart-bar me-1"></i> Estadísticas Visuales (Próximamente)</div>
                <div class="card-body text-center py-5">
                    <p class="text-muted">Aquí se mostrarán gráficas sobre el progreso de los cursos o actividad de estudiantes.</p>
                    <i class="fas fa-palette fa-4x text-light"></i>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
