@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-graduation-cap me-2"></i>Carreras y Cursos</h1>
    </div>

    {{-- Mensajes Flash --}}
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

    {{-- SECCIÓN 1: MIS CURSOS INSCRITOS POR CARRERA --}}
    @if(isset($carrerasConCursosActivos) && $carrerasConCursosActivos->count() > 0)
        <h3 class="mb-3 display-6"><i class="fas fa-bookmark me-2 text-success"></i>Mis Cursos Inscritos</h3>
        <p class="text-muted">Estos son los cursos en los que estás actualmente inscrito y activo.</p>
        @foreach ($carrerasConCursosActivos as $carreraInscrita)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">{{ $carreraInscrita->nombre }}</h5>
                </div>
                <div class="list-group list-group-flush">
                    @php
                        // Obtener los cursos de esta carrera en los que el estudiante está inscrito
                        $cursosParaEstaCarrera = isset($cursosActivosPorCarreraId) ? $cursosActivosPorCarreraId->get($carreraInscrita->id) : collect();
                        if (is_null($cursosParaEstaCarrera)) $cursosParaEstaCarrera = collect(); // Asegurar que sea una colección
                    @endphp
                    @forelse ($cursosParaEstaCarrera as $curso)
                        <a href="{{ route('alumno.cursos.show', $curso->id) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
                            <div>
                                <span class="fw-bold fs-5">{{ $curso->titulo }}</span>
                                <br>
                                <small class="text-muted">
                                    Profesor(es):
                                    @if($curso->profesores && $curso->profesores->count() > 0)
                                        {{ $curso->profesores->pluck('nombre')->implode(', ') }}
                                    @else
                                        N/A
                                    @endif
                                </small>
                            </div>
                            <i class="fas fa-chevron-right text-muted"></i>
                        </a>
                    @empty
                        <li class="list-group-item text-muted">No tienes cursos activos en esta carrera actualmente.</li>
                    @endforelse
                </div>
            </div>
        @endforeach
        <hr class="my-5">
    @else
        <div class="alert alert-light text-center border shadow-sm mb-4 p-4">
            <i class="fas fa-info-circle fa-2x text-primary mb-2"></i>
            <p class="mb-1 fs-5">Aún no estás inscrito en ningún curso.</p>
            <small class="text-muted">Explora las carreras disponibles a continuación.</small>
        </div>
    @endif

    {{-- SECCIÓN 2: OTRAS CARRERAS DISPONIBLES PARA EXPLORAR --}}
    <h3 class="mb-3 display-6"><i class="fas fa-search me-2 text-primary"></i>Explorar Todas las Carreras</h3>
    <p class="text-muted">Selecciona una carrera para ver los cursos que ofrece.</p>
    @if(isset($todasLasCarrerasParaExplorar) && $todasLasCarrerasParaExplorar->count() > 0)
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            @foreach ($todasLasCarrerasParaExplorar as $carrera)
                <div class="col d-flex align-items-stretch">
                    <div class="card h-100 shadow-sm hover-shadow-lg transition-fast">
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 150px;">
                            <i class="fas fa-university fa-3x text-secondary opacity-50"></i>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold">{{ $carrera->nombre }}</h5>
                            @if($carrera->descripcion)
                                <p class="card-text small text-muted flex-grow-1">{{ Str::limit($carrera->descripcion, 100) }}</p>
                            @else
                                <p class="card-text small text-muted flex-grow-1"><i>Descripción no disponible.</i></p>
                            @endif
                            <div class="mt-auto">
                                <a href="{{ route('alumno.cursos.index', $carrera->id) }}" class="btn btn-primary w-100">
                                    Ver Cursos
                                    @if($carrera->cursos_count > 0)
                                        <span class="badge bg-white text-primary ms-1">{{ $carrera->cursos_count }}</span>
                                    @else
                                        <span class="badge bg-light text-secondary ms-1">0</span>
                                    @endif
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-light text-center shadow-sm">
            <p class="mb-0">No hay otras carreras disponibles para explorar en este momento.</p>
        </div>
    @endif

    @if((!isset($carrerasConCursosActivos) || $carrerasConCursosActivos->isEmpty()) && (!isset($todasLasCarrerasParaExplorar) || $todasLasCarrerasParaExplorar->isEmpty()))
        <div class="alert alert-info text-center shadow-sm p-4">
            <h4 class="alert-heading"><i class="fas fa-info-circle me-2"></i>No hay carreras disponibles.</h4>
            <p>Por favor, vuelve a intentarlo más tarde o contacta al administrador.</p>
        </div>
    @endif

</div>

<style>
    .hover-shadow-lg {
        transition: box-shadow .2s ease-in-out, transform .2s ease-in-out;
    }
    .hover-shadow-lg:hover {
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
        transform: translateY(-3px);
    }
</style>
@endsection
