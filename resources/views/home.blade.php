@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            {{-- Mensajes Flash --}}
            @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Tarjeta de Bienvenida --}}
            <div class="card shadow-sm border-0 mb-4 overflow-hidden">
                <div class="card-body p-0">
                    <div class="row g-0">
                        {{-- Imagen de Fondo/Decorativa --}}
                        <div class="col-md-4 bg-primary bg-gradient d-flex align-items-center justify-content-center py-5">
                            <img src="{{ secure_asset('images/logo_mitec.png') }}" alt="{{ config('app.name', 'MiTec') }} Logo" class="img-fluid p-4" style="max-width: 200px;" onerror="this.onerror=null; this.src='{{ secure_asset('images/default_logo.png') }}';">
                        </div>
                        
                        {{-- Contenido de Bienvenida --}}
                        <div class="col-md-8">
                            <div class="p-4 p-md-5">
                                <h2 class="display-6 fw-bold mb-3">¡Bienvenido, {{ Auth::user()->nombre }}!</h2>
                                <p class="lead text-muted mb-4">
                                    @if(Auth::user()->tieneRole('docente'))
                                        Accede a tu panel de docente para gestionar tus cursos y estudiantes.
                                    @elseif(Auth::user()->tieneRole('estudiante'))
                                        Explora tus cursos y mantente al día con tus actividades académicas.
                                    @else
                                        Gracias por iniciar sesión en MiTec.
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Acciones Principales --}}
            <div class="row g-4">
                @if(Auth::user()->tieneRole('docente'))
                    {{-- Panel Docente --}}
                    <div class="col-md-6">
                        <div class="card h-100 shadow-sm hover-shadow-md border-0">
                            <div class="card-body p-4 text-center">
                                <div class="rounded-circle bg-primary bg-opacity-10 p-3 d-inline-block mb-3">
                                    <i class="fas fa-chalkboard-teacher fa-2x text-primary"></i>
                                </div>
                                <h4 class="card-title mb-3">Panel de Docente</h4>
                                <p class="card-text text-muted mb-4">
                                    Accede a tu dashboard para gestionar tus cursos, estudiantes y materiales educativos.
                                </p>
                                <a href="{{ route('docente.dashboard') }}" class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-columns me-2"></i>Ir al Dashboard
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100 shadow-sm hover-shadow-md border-0">
                            <div class="card-body p-4 text-center">
                                <div class="rounded-circle bg-info bg-opacity-10 p-3 d-inline-block mb-3">
                                    <i class="fas fa-book-reader fa-2x text-info"></i>
                                </div>
                                <h4 class="card-title mb-3">Gestión de Cursos</h4>
                                <p class="card-text text-muted mb-4">
                                    Administra tus cursos, crea contenido y realiza un seguimiento del progreso.
                                </p>
                                <a href="{{ route('docente.cursos.index') }}" class="btn btn-info btn-lg w-100 text-white">
                                    <i class="fas fa-edit me-2"></i>Gestionar Cursos
                                </a>
                            </div>
                        </div>
                    </div>
                @elseif(Auth::user()->tieneRole('estudiante'))
                    {{-- Panel Estudiante --}}
                    <div class="col-md-6">
                        <div class="card h-100 shadow-sm hover-shadow-md border-0">
                            <div class="card-body p-4 text-center">
                                <div class="rounded-circle bg-success bg-opacity-10 p-3 d-inline-block mb-3">
                                    <i class="fas fa-user-graduate fa-2x text-success"></i>
                                </div>
                                <h4 class="card-title mb-3">Panel de Estudiante</h4>
                                <p class="card-text text-muted mb-4">
                                    Accede a tu espacio personal para ver tus cursos y actividades pendientes.
                                </p>
                                <a href="{{ route('alumno.dashboard') }}" class="btn btn-success btn-lg w-100">
                                    <i class="fas fa-columns me-2"></i>Ir al Dashboard
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100 shadow-sm hover-shadow-md border-0">
                            <div class="card-body p-4 text-center">
                                <div class="rounded-circle bg-primary bg-opacity-10 p-3 d-inline-block mb-3">
                                    <i class="fas fa-graduation-cap fa-2x text-primary"></i>
                                </div>
                                <h4 class="card-title mb-3">Explorar Carreras</h4>
                                <p class="card-text text-muted mb-4">
                                    Descubre y explora las carreras y cursos disponibles en nuestra plataforma.
                                </p>
                                <a href="{{ route('alumno.carreras.index') }}" class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-search me-2"></i>Ver Carreras
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Acciones Rápidas --}}
            <div class="card shadow-sm border-0 mt-4">
                <div class="card-body p-4">
                    <h5 class="card-title mb-4">Acciones Rápidas</h5>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('perfil.show') }}" class="btn btn-light">
                            <i class="fas fa-user-circle me-2"></i>Mi Perfil
                        </a>
                        @if(Auth::user()->tieneRole('estudiante'))
                            <a href="{{ route('alumno.agenda.index') }}" class="btn btn-light">
                                <i class="fas fa-calendar-alt me-2"></i>Mi Agenda
                            </a>
                            <a href="{{ route('alumno.calificaciones.index') }}" class="btn btn-light">
                                <i class="fas fa-star me-2"></i>Calificaciones
                            </a>
                        @endif
                        <a href="{{ route('logout') }}" class="btn btn-light text-danger" 
                           onclick="event.preventDefault(); document.getElementById('logout-form-home').submit();">
                            <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                        </a>
                        <form id="logout-form-home" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hover-shadow-md {
    transition: box-shadow 0.3s ease;
}
.hover-shadow-md:hover {
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.1)!important;
}
</style>
@endsection