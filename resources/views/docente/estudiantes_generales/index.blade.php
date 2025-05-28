@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="display-5 mb-0">Mis Estudiantes</h1>
            <p class="text-muted mb-0">Gestiona todos tus estudiantes inscritos</p>
        </div>
        <a href="{{ route('docente.dashboard') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-2"></i>Dashboard
        </a>
    </div>

    {{-- Formulario de Búsqueda --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('docente.estudiantes.generales') }}">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" 
                           class="form-control border-start-0 ps-0" 
                           name="busqueda" 
                           placeholder="Buscar por nombre, apellido o email..." 
                           value="{{ $terminoBusqueda ?? '' }}"
                           aria-label="Término de búsqueda">
                    <button class="btn btn-primary px-4" type="submit">Buscar</button>
                    @if(isset($terminoBusqueda) && $terminoBusqueda)
                        <a href="{{ route('docente.estudiantes.generales') }}" class="btn btn-light">
                            <i class="fas fa-times me-1"></i>Limpiar
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @if($inscripciones && $inscripciones->count() > 0)
        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0">Estudiante</th>
                            <th class="border-0">Contacto</th>
                            <th class="border-0">Curso</th>
                            <th class="border-0">Fecha Inscripción</th>
                            <th class="border-0 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($inscripciones as $inscripcion)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle bg-primary text-white me-3">
                                            {{ substr(optional($inscripcion->estudiante)->nombre, 0, 1) }}
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ optional($inscripcion->estudiante)->apellidos }}, {{ optional($inscripcion->estudiante)->nombre }}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center text-muted">
                                        <i class="far fa-envelope me-2"></i>
                                        {{ optional($inscripcion->estudiante)->email }}
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ route('docente.cursos.show', optional($inscripcion->curso)->id) }}" 
                                       class="text-decoration-none d-flex align-items-center">
                                        <div class="course-icon me-2">
                                            <i class="fas fa-book"></i>
                                        </div>
                                        {{ optional($inscripcion->curso)->titulo }}
                                    </a>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="far fa-calendar-alt text-muted me-2"></i>
                                        {{ $inscripcion->fecha_inscripcion ? $inscripcion->fecha_inscripcion->format('d/m/Y') : 'N/A' }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('docente.cursos.estudiantes.show', [optional($inscripcion->curso)->id, optional($inscripcion->estudiante)->id]) }}" 
                                       class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-chart-line me-1"></i> Ver Progreso
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($inscripciones->hasPages())
                <div class="card-footer bg-white border-top-0 pt-0">
                    {{ $inscripciones->appends(['busqueda' => $terminoBusqueda])->links() }}
                </div>
            @endif
        </div>
    @else
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                @if(isset($terminoBusqueda) && $terminoBusqueda)
                    <div class="text-muted">
                        <i class="fas fa-search fa-3x mb-3"></i>
                        <h5>Sin resultados</h5>
                        <p>No se encontraron estudiantes que coincidan con "<strong>{{ $terminoBusqueda }}</strong>".</p>
                    </div>
                @else
                    <div class="text-primary">
                        <i class="fas fa-users fa-3x mb-3"></i>
                        <h5>Sin estudiantes inscritos</h5>
                        <p class="mb-0">Aún no tienes estudiantes con inscripciones activas en tus cursos.</p>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    /* Estilos generales */
    .card {
        border: none;
        border-radius: 0.75rem;
    }
    
    .card-footer {
        background: transparent;
    }

    /* Avatar circular para iniciales */
    .avatar-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 500;
        font-size: 1.1rem;
    }

    /* Icono del curso */
    .course-icon {
        width: 32px;
        height: 32px;
        border-radius: 0.5rem;
        background-color: #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6c757d;
    }

    /* Mejoras en la tabla */
    .table > :not(caption) > * > * {
        padding: 1rem 1.25rem;
    }

    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.025em;
        color: #6c757d;
    }

    .table td {
        vertical-align: middle;
    }

    /* Mejoras en los botones */
    .btn {
        font-weight: 500;
        padding: 0.5rem 1rem;
        transition: all 0.2s;
    }

    .btn:hover {
        transform: translateY(-1px);
    }

    .btn-sm {
        padding: 0.25rem 0.75rem;
    }

    /* Input group personalizado */
    .input-group {
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .input-group-text {
        border-radius: 0.5rem 0 0 0.5rem;
        border: 1px solid #dee2e6;
    }

    .input-group .form-control {
        border-radius: 0 0.5rem 0.5rem 0;
        border: 1px solid #dee2e6;
    }

    .input-group .btn {
        margin-left: 0.5rem;
        border-radius: 0.5rem;
    }

    /* Enlaces y textos */
    a {
        color: #0d6efd;
        text-decoration: none;
    }

    a:hover {
        color: #0a58ca;
    }

    .text-muted {
        color: #6c757d !important;
    }

    /* Animaciones hover */
    .table tbody tr {
        transition: background-color 0.2s;
    }

    .table tbody tr:hover {
        background-color: rgba(0,0,0,0.01);
    }

    /* Icono de correo */
    .far.fa-envelope {
        font-size: 0.875rem;
    }

    /* Display titles */
    .display-5 {
        font-weight: 600;
        font-size: 2rem;
    }

    /* Responsive ajustments */
    @media (max-width: 768px) {
        .avatar-circle {
            width: 32px;
            height: 32px;
            font-size: 1rem;
        }

        .table > :not(caption) > * > * {
            padding: 0.75rem 1rem;
        }
    }
</style>
@endpush
