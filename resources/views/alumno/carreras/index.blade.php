@extends('layouts.app')

@section('content')
<div class="container py-4">
    {{-- Encabezado Principal --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="display-6 mb-0"><i class="fas fa-graduation-cap me-2"></i>Carreras y Cursos</h1>
                            <p class="mb-0 mt-2 opacity-75">Explora las carreras disponibles y gestiona tus cursos</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Mensajes Flash --}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- SECCIÓN 1: MIS CURSOS INSCRITOS POR CARRERA --}}
    @if(isset($carrerasConCursosActivos) && $carrerasConCursosActivos->count() > 0)
        <div class="mb-5">
            <h3 class="mb-3 display-6">
                <i class="fas fa-bookmark me-2 text-success"></i>Mis Cursos Inscritos
            </h3>
            <p class="text-muted mb-4">Estos son los cursos en los que estás actualmente inscrito y activo.</p>
            
            <div class="row g-4">
                @foreach ($carrerasConCursosActivos as $carreraInscrita)
                    <div class="col-12">
                        <div class="card shadow-sm hover-shadow-sm">
                            <div class="card-header bg-white border-bottom py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">
                                        <i class="fas fa-university me-2 text-primary"></i>{{ $carreraInscrita->nombre }}
                                    </h5>
                                    @php
                                        $cursosParaEstaCarrera = isset($cursosActivosPorCarreraId) ? $cursosActivosPorCarreraId->get($carreraInscrita->id) : collect();
                                        if (is_null($cursosParaEstaCarrera)) $cursosParaEstaCarrera = collect();
                                    @endphp
                                    <span class="badge bg-primary">{{ $cursosParaEstaCarrera->count() }} cursos</span>
                                </div>
                            </div>
                            <div class="list-group list-group-flush">
                                @forelse ($cursosParaEstaCarrera as $curso)
                                    <a href="{{ route('alumno.cursos.show', $curso->id) }}" 
                                       class="list-group-item list-group-item-action p-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1 fs-5">{{ $curso->titulo }}</h6>
                                                <div class="text-muted small">
                                                    <i class="fas fa-user-tie me-1"></i>
                                                    @if($curso->profesores && $curso->profesores->count() > 0)
                                                        {{ $curso->profesores->pluck('nombre')->implode(', ') }}
                                                    @else
                                                        Profesor no asignado
                                                    @endif
                                                </div>
                                            </div>
                                            <i class="fas fa-chevron-right text-primary"></i>
                                        </div>
                                    </a>
                                @empty
                                    <div class="list-group-item text-center py-4">
                                        <i class="fas fa-info-circle fa-2x text-muted mb-2"></i>
                                        <p class="mb-0 text-muted">No tienes cursos activos en esta carrera actualmente.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <hr class="my-5">
    @else
        <div class="alert alert-light text-center border shadow-sm mb-5 p-4">
            <i class="fas fa-info-circle fa-3x text-primary mb-3"></i>
            <h5 class="mb-2">Aún no estás inscrito en ningún curso</h5>
            <p class="text-muted mb-0">Explora las carreras disponibles a continuación y comienza tu aprendizaje.</p>
        </div>
    @endif

    {{-- SECCIÓN 2: OTRAS CARRERAS DISPONIBLES PARA EXPLORAR --}}
    <div>
        <h3 class="mb-3 display-6">
            <i class="fas fa-search me-2 text-primary"></i>Explorar Todas las Carreras
        </h3>
        <p class="text-muted mb-4">Selecciona una carrera para ver los cursos que ofrece.</p>

        @if(isset($todasLasCarrerasParaExplorar) && $todasLasCarrerasParaExplorar->count() > 0)
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                @foreach ($todasLasCarrerasParaExplorar as $carrera)
                    <div class="col">
                        <div class="card h-100 shadow-sm hover-shadow-lg">
                            {{-- Imagen/Icono de la carrera --}}
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center py-4">
                                <div class="text-center">
                                    <i class="fas fa-university fa-3x text-primary opacity-75 mb-2"></i>
                                    @if($carrera->cursos_count > 0)
                                        <div class="badge bg-primary">
                                            {{ $carrera->cursos_count }} curso{{ $carrera->cursos_count !== 1 ? 's' : '' }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title fw-bold mb-3">{{ $carrera->nombre }}</h5>
                                @if($carrera->descripcion)
                                    <p class="card-text text-muted small flex-grow-1">
                                        {{ Str::limit($carrera->descripcion, 100) }}
                                    </p>
                                @else
                                    <p class="card-text text-muted small flex-grow-1">
                                        <i>Descripción no disponible.</i>
                                    </p>
                                @endif
                                
                                <div class="mt-auto pt-3">
                                    <a href="{{ route('alumno.cursos.index', $carrera->id) }}" 
                                       class="btn btn-primary w-100">
                                        <i class="fas fa-door-open me-2"></i>Explorar Cursos
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-light text-center border shadow-sm p-4">
                <i class="fas fa-school fa-3x text-muted mb-3"></i>
                <h5 class="mb-2">No hay carreras disponibles</h5>
                <p class="text-muted mb-0">No hay carreras disponibles para explorar en este momento.</p>
            </div>
        @endif
    </div>

    @if((!isset($carrerasConCursosActivos) || $carrerasConCursosActivos->isEmpty()) && 
        (!isset($todasLasCarrerasParaExplorar) || $todasLasCarrerasParaExplorar->isEmpty()))
        <div class="alert alert-info text-center shadow-sm p-5">
            <i class="fas fa-info-circle fa-3x mb-3"></i>
            <h4 class="alert-heading">No hay carreras disponibles</h4>
            <p class="mb-0">Por favor, vuelve a intentarlo más tarde o contacta al administrador.</p>
        </div>
    @endif
</div>

<style>
.hover-shadow-lg {
    transition: all 0.2s ease-in-out;
}
.hover-shadow-lg:hover {
    transform: translateY(-3px);
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
}
.hover-shadow-sm {
    transition: all 0.2s ease-in-out;
}
.hover-shadow-sm:hover {
    transform: translateY(-2px);
    box-shadow: 0 .3rem .5rem rgba(0,0,0,.08)!important;
}
.card-img-top {
    min-height: 160px;
}
.badge {
    font-weight: 500;
}
</style>
@endsection
