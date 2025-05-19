@extends('layouts.app')

@section('content')
<div class="container py-4">
    {{-- Breadcrumbs para mejor navegación --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('alumno.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('alumno.carreras.index') }}">Carreras</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $carrera->nombre ?? 'Cursos' }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <h1><i class="fas fa-book-reader me-2"></i>Cursos en: <span class="text-primary">{{ $carrera->nombre ?? 'Carrera no especificada' }}</span></h1>
        <a href="{{ route('alumno.carreras.index') }}" class="btn btn-outline-secondary btn-sm mt-2 mt-md-0">
            <i class="fas fa-arrow-left me-1"></i> Ver Todas las Carreras
        </a>
    </div>

    {{-- Mensajes Flash --}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(isset($cursosDeLaCarrera) && $cursosDeLaCarrera->count() > 0)
        <p class="text-muted mb-4">Explora los cursos disponibles para la carrera de <strong>{{ $carrera->nombre ?? '' }}</strong> y solicita tu inscripción.</p>
        {{-- vvv MODIFICADO: Añadido row-cols-xl-4 para pantallas más grandes vvv --}}
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4">
            @foreach ($cursosDeLaCarrera as $curso)
                <div class="col d-flex align-items-stretch">
                    <div class="card h-100 shadow-sm hover-shadow-lg transition-fast">
                        @if($curso->ruta_imagen_curso)
                            <img src="{{ Storage::url($curso->ruta_imagen_curso) }}" class="card-img-top" alt="Imagen de {{ $curso->titulo }}" style="height: 160px; object-fit: cover;"> {{-- Altura de imagen reducida un poco --}}
                        @else
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 160px;">
                                <i class="fas fa-chalkboard-teacher fa-3x text-secondary opacity-25"></i>
                            </div>
                        @endif
                        <div class="card-body d-flex flex-column pb-3 pt-2 px-3">
                            <h6 class="card-title fw-bold mb-1">{{ $curso->titulo }}</h6>
                            <p class="card-text small text-muted mb-2">
                                @if($curso->codigo_curso)
                                    <span class="me-2"><i class="fas fa-barcode fa-fw me-1 opacity-75"></i>{{ $curso->codigo_curso }}</span>
                                @endif
                                <br><i class="fas fa-user-tie fa-fw me-1 opacity-75"></i>
                                @if($curso->profesores && $curso->profesores->count() > 0)
                                    {{ Str::limit($curso->profesores->pluck('nombre')->implode(', '), 25) }}
                                @else
                                    Profesor no asignado
                                @endif
                            </p>
                            <p class="card-text flex-grow-1 mb-2 small" style="font-size: 0.85rem;">{{ Str::limit($curso->descripcion, 90) }}</p>

                            <div class="mt-auto pt-2 border-top">
                                @php
                                    $inscripcion = isset($inscripcionesEstudiante) ? $inscripcionesEstudiante->get($curso->id) : null;
                                @endphp

                                @if($inscripcion)
                                    @if($inscripcion->estado == 'activo')
                                        <a href="{{ route('alumno.cursos.show', $curso->id) }}" class="btn btn-success btn-sm w-100">
                                            <i class="fas fa-eye me-1"></i>Ver Contenido
                                        </a>
                                        <div class="text-center mt-1"><span class="badge bg-success-light text-success border px-2 py-1" style="font-size: 0.75em;">Inscrito</span></div>
                                    @elseif($inscripcion->estado == 'pendiente')
                                        <button type="button" class="btn btn-outline-secondary btn-sm w-100 disabled">
                                            <i class="fas fa-clock me-1"></i>Solicitud Pendiente
                                        </button>
                                    @else
                                         <a href="{{ route('alumno.cursos.show', $curso->id) }}" class="btn btn-outline-secondary btn-sm w-100">Ver Curso</a>
                                         <div class="text-center mt-1"><span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.75em;">{{ ucfirst($inscripcion->estado) }}</span></div>
                                    @endif
                                @else
                                    <form action="{{ route('alumno.cursos.solicitar', $curso->id) }}" method="POST" class="d-grid">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus-circle me-1"></i>Solicitar Inscripción
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Paginación --}}
        <div class="mt-4 pt-2 d-flex justify-content-center">
            {{ $cursosDeLaCarrera->links() }}
        </div>

    @else
        <div class="alert alert-info text-center shadow-sm p-4">
            <h4 class="alert-heading"><i class="fas fa-info-circle me-2"></i>No hay cursos disponibles</h4>
            <p>Actualmente no hay cursos publicados para la carrera de <strong>{{ $carrera->nombre ?? 'esta carrera' }}</strong>.</p>
             <a href="{{ route('alumno.carreras.index') }}" class="btn btn-outline-primary mt-2">
                <i class="fas fa-arrow-left me-1"></i> Ver otras carreras
            </a>
        </div>
    @endif
</div>

{{-- Estilos opcionales (si no están globales) --}}
<style>
    .hover-shadow-lg {
        transition: box-shadow .2s ease-in-out, transform .2s ease-in-out;
    }
    .hover-shadow-lg:hover {
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
        transform: translateY(-3px);
    }
    .badge.bg-success-light {
        color: #0f5132;
        background-color: #d1e7dd;
    }
</style>
@endsection
