@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Cursos Disponibles</h1>

    {{-- Mensajes de estado/error --}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($cursosDisponibles && $cursosDisponibles->count() > 0)
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            @foreach ($cursosDisponibles as $curso)
                <div class="col">
                    <div class="card h-100">
                        {{-- <img src="..." class="card-img-top" alt="..."> --}}
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $curso->titulo }}</h5>
                            <p class="card-text flex-grow-1">{{ Str::limit($curso->descripcion, 100) }}</p>
                            <p class="card-text">
                                <small class="text-muted">
                                    Profesor(es):
                                    @if($curso->profesores && $curso->profesores->count() > 0)
                                        {{ $curso->profesores->pluck('nombre')->implode(', ') }}
                                    @else
                                        No asignado
                                    @endif
                                </small>
                            </p>

                            {{-- Lógica para mostrar estado/botón de inscripción --}}
                            <div class="mt-auto">
                                {{-- Verifica si existe una inscripción para este curso en la colección del estudiante --}}
                                @php
                                    $inscripcion = $inscripcionesEstudiante->get($curso->id);
                                @endphp

                                @if($inscripcion)
                                    {{-- Si existe la inscripción, verifica su estado --}}
                                    @if($inscripcion->estado == 'activo')
                                        {{-- Si está activo, puede ver el curso --}}
                                        <a href="{{ route('alumno.cursos.show', $curso->id) }}" class="btn btn-success">Ver Curso</a>
                                        <span class="badge bg-success ms-2">Inscrito</span>
                                    @elseif($inscripcion->estado == 'pendiente')
                                        {{-- Si está pendiente, muestra estado pendiente --}}
                                        <button type="button" class="btn btn-secondary disabled">Solicitud Pendiente</button>
                                    @else
                                         {{-- Otros estados (completado, abandonado) podrían mostrarse diferente --}}
                                         <a href="{{ route('alumno.cursos.show', $curso->id) }}" class="btn btn-secondary">Ver Curso</a>
                                         <span class="badge bg-light text-dark ms-2">{{ ucfirst($inscripcion->estado) }}</span>
                                    @endif
                                @else
                                    {{-- Si no existe inscripción, muestra formulario para solicitar --}}
                                    <form action="{{ route('alumno.cursos.solicitar', $curso->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-primary">Solicitar Inscripción</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Paginación --}}
        <div class="mt-4">
            {{ $cursosDisponibles->links() }}
        </div>

    @else
        <div class="alert alert-info">
            No hay cursos disponibles publicados en este momento.
        </div>
    @endif
</div>
@endsection
