@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Panel del Estudiante</h1>

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
        <div class="col-md-7 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <i class="fas fa-book me-1"></i> Mis Cursos Activos
                </div>
                <div class="card-body">
                    @if($cursosActivos && $cursosActivos->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($cursosActivos as $curso)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <a href="{{ route('alumno.cursos.show', $curso->id) }}" class="fw-bold text-decoration-none">{{ $curso->titulo }}</a>
                                        <br>
                                        <small class="text-muted">
                                            Profesor(es): {{ $curso->profesores->pluck('nombre')->implode(', ') ?: 'N/A' }}
                                        </small>
                                    </div>
                                    <a href="{{ route('alumno.cursos.show', $curso->id) }}" class="btn btn-sm btn-outline-primary">Ir al Curso</a>
                                </li>
                            @endforeach
                        </ul>
                        {{-- Enlace a ver todos los cursos --}}
                        @if(Auth::user()->cursosInscritos()->wherePivot('estado', 'activo')->count() > $cursosActivos->count())
                         <div class="text-center mt-3">
                             <a href="{{ route('alumno.cursos.index') }}" class="btn btn-sm btn-light">Ver todos mis cursos</a>
                         </div>
                        @endif
                    @else
                        <p class="text-muted">No estás inscrito en ningún curso activo.</p>
                        <a href="{{ route('alumno.cursos.index') }}" class="btn btn-primary">Explorar Cursos Disponibles</a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Columna Próximas Tareas --}}
        <div class="col-md-5 mb-4">
            <div class="card h-100">
                 <div class="card-header">
                    <i class="fas fa-calendar-check me-1"></i> Próximas Tareas
                </div>
                <div class="card-body">
                     @if($proximasTareas && $proximasTareas->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($proximasTareas as $tarea)
                                <li class="list-group-item">
                                    <a href="{{ route('alumno.cursos.tareas.show', [$tarea->curso->id, $tarea->id]) }}" class="text-decoration-none">{{ $tarea->titulo }}</a>
                                    <br>
                                    <small class="text-muted">
                                        Curso: {{ $tarea->curso->titulo }} <br>
                                        Vence: <span class="fw-bold">{{ $tarea->fecha_limite ? $tarea->fecha_limite->format('d/m/Y H:i') : 'Sin fecha' }}</span>
                                        ({{ $tarea->fecha_limite ? $tarea->fecha_limite->diffForHumans() : '' }})
                                    </small>
                                </li>
                            @endforeach
                        </ul>
                     @else
                         <p class="text-muted">No tienes tareas próximas.</p>
                     @endif
                </div>
            </div>
        </div>
    </div> {{-- Fin row --}}

    {{-- Aquí podrías añadir más secciones: Anuncios recientes, últimas calificaciones, etc. --}}

</div>
@endsection
