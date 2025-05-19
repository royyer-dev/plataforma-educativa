@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0 py-2"><i class="fas fa-tachometer-alt me-2"></i>{{ __('Panel Principal') }}</h4>
                </div>

                <div class="card-body p-4 p-md-5 text-center">
                    @if (session('status'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('status') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <h3 class="mb-3">¡Bienvenido de nuevo, {{ Auth::user()->nombre }}!</h3>
                    <p class="lead text-muted">Has iniciado sesión en MiTec.</p>
                    <hr class="my-4">

                    <p>¿Qué te gustaría hacer hoy?</p>

                    <div class="mt-4">
                        @if(Auth::user()->tieneRole('docente'))
                            {{-- Acciones para Docentes --}}
                            <a href="{{ route('docente.dashboard') }}" class="btn btn-lg btn-info text-white mx-1 mb-2">
                                <i class="fas fa-chalkboard-teacher me-2"></i>Ir a mi Panel de Docente
                            </a>
                            <a href="{{ route('docente.cursos.index') }}" class="btn btn-lg btn-outline-secondary mx-1 mb-2">
                                <i class="fas fa-edit me-2"></i>Gestionar Mis Cursos
                            </a>
                        @elseif(Auth::user()->tieneRole('estudiante'))
                            {{-- Acciones para Estudiantes --}}
                            <a href="{{ route('alumno.dashboard') }}" class="btn btn-lg btn-success text-white mx-1 mb-2">
                                <i class="fas fa-user-graduate me-2"></i>Ir a mi Panel de Estudiante
                            </a>
                            <a href="{{ route('alumno.carreras.index') }}" class="btn btn-lg btn-outline-secondary mx-1 mb-2">
                                <i class="fas fa-search me-2"></i>Explorar Carreras y Cursos
                            </a>
                        @else
                            {{-- Para usuarios sin un rol específico de docente o estudiante, o si la redirección falló --}}
                            <p class="text-muted">No tienes un panel específico asignado. Puedes explorar las opciones generales.</p>
                        @endif

                        {{-- Enlace común para todos los usuarios autenticados --}}
                        <div class="mt-4">
                            <a href="{{ route('perfil.show') }}" class="btn btn-outline-primary mx-1 mb-2">
                                <i class="fas fa-user-circle me-2"></i>Ver Mi Perfil
                            </a>
                            <a href="{{ route('logout') }}" class="btn btn-outline-danger mx-1 mb-2"
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
</div>
@endsection
