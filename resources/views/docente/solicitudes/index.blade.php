@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="display-6 mb-1">Solicitudes de Inscripción</h1>
                    <p class="text-muted mb-0">Gestiona las solicitudes de tus cursos</p>
                </div>
                <a href="{{ route('docente.dashboard') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i>Dashboard
                </a>
            </div>

            {{-- Mensajes Flash --}}
            @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Sección Solicitudes Pendientes --}}
            <div class="card shadow-sm mb-5">
                <div class="card-header d-flex align-items-center bg-warning bg-opacity-10 border-warning border-opacity-25 border-start border-4 py-3">
                    <div class="status-icon text-warning me-3">
                        <i class="fas fa-hourglass-half fa-lg"></i>
                    </div>
                    <h5 class="mb-0 text-warning">Solicitudes Pendientes</h5>
                </div>
                <div class="card-body p-0">
                    @if(isset($solicitudesPendientes) && $solicitudesPendientes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Estudiante</th>
                                        <th>Curso Solicitado</th>
                                        <th>Carrera</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($solicitudesPendientes as $solicitud)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle bg-primary text-white me-3">
                                                        {{ substr(optional($solicitud->estudiante)->nombre, 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ optional($solicitud->estudiante)->nombre }} {{ optional($solicitud->estudiante)->apellidos }}</h6>
                                                        <small class="text-muted">{{ optional($solicitud->estudiante)->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="{{ route('docente.cursos.show', optional($solicitud->curso)->id) }}" 
                                                   class="text-decoration-none">
                                                    <div class="d-flex align-items-center">
                                                        <div class="course-icon me-2">
                                                            <i class="fas fa-book"></i>
                                                        </div>
                                                        {{ Str::limit(optional($solicitud->curso)->titulo, 30) }}
                                                    </div>
                                                </a>
                                            </td>
                                            <td>{{ optional(optional($solicitud->curso)->carrera)->nombre ?? 'N/A' }}</td>
                                            <td>
                                                <div class="d-flex justify-content-center gap-2">
                                                    <form action="{{ route('docente.solicitudes.aprobar', $solicitud->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-success btn-sm">
                                                            <i class="fas fa-check me-1"></i>Aprobar
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('docente.solicitudes.rechazar', $solicitud->id) }}" 
                                                          method="POST" 
                                                          class="d-inline"
                                                          onsubmit="return confirm('¿Estás seguro de querer rechazar esta solicitud?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm">
                                                            <i class="fas fa-times me-1"></i>Rechazar
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="text-warning mb-3">
                                <i class="fas fa-inbox fa-3x"></i>
                            </div>
                            <h5>Sin solicitudes pendientes</h5>
                            <p class="text-muted mb-0">No hay solicitudes de inscripción pendientes en este momento.</p>
                        </div>
                    @endif
                </div>
                @if(isset($solicitudesPendientes) && $solicitudesPendientes->hasPages())
                    <div class="card-footer bg-transparent border-0">
                        {{ $solicitudesPendientes->appends(request()->except('pendientes_page'))->links() }}
                    </div>
                @endif
            </div>

            {{-- Sección Historial --}}
            <div class="card shadow-sm">
                <div class="card-header d-flex align-items-center bg-success bg-opacity-10 border-success border-opacity-25 border-start border-4 py-3">
                    <div class="status-icon text-success me-3">
                        <i class="fas fa-history fa-lg"></i>
                    </div>
                    <h5 class="mb-0 text-success">Historial de Inscripciones</h5>
                </div>
                <div class="card-body p-0">
                    @if(isset($solicitudesAceptadas) && $solicitudesAceptadas->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Estudiante</th>
                                        <th>Curso</th>
                                        <th>Carrera</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($solicitudesAceptadas as $solicitud)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="far fa-calendar-check text-muted me-2"></i>
                                                    {{ $solicitud->fecha_inscripcion ? Carbon\Carbon::parse($solicitud->fecha_inscripcion)->format('d/m/Y H:i') : $solicitud->updated_at->format('d/m/Y H:i') }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle bg-success text-white me-3">
                                                        {{ substr(optional($solicitud->estudiante)->nombre, 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ optional($solicitud->estudiante)->nombre }} {{ optional($solicitud->estudiante)->apellidos }}</h6>
                                                        <small class="text-muted">{{ optional($solicitud->estudiante)->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="{{ route('docente.cursos.show', optional($solicitud->curso)->id) }}" 
                                                   class="text-decoration-none">
                                                    <div class="d-flex align-items-center">
                                                        <div class="course-icon me-2">
                                                            <i class="fas fa-book"></i>
                                                        </div>
                                                        {{ Str::limit(optional($solicitud->curso)->titulo, 30) }}
                                                    </div>
                                                </a>
                                            </td>
                                            <td>{{ optional(optional($solicitud->curso)->carrera)->nombre ?? 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="text-success mb-3">
                                <i class="fas fa-clipboard-list fa-3x"></i>
                            </div>
                            <h5>Sin historial</h5>
                            <p class="text-muted mb-0">Aún no has aceptado ninguna solicitud de inscripción.</p>
                        </div>
                    @endif
                </div>
                @if(isset($solicitudesAceptadas) && $solicitudesAceptadas->hasPages())
                    <div class="card-footer bg-transparent border-0">
                        {{ $solicitudesAceptadas->appends(request()->except('aceptadas_page'))->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Estilos generales */
    .card {
        border: none;
        border-radius: 1rem;
        overflow: hidden;
    }

    /* Avatar circular */
    .avatar-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 500;
    }

    /* Icono de curso */
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

    /* Cabeceras de sección */
    .status-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Tabla */
    .table > :not(caption) > * > * {
        padding: 1rem 1.25rem;
    }

    .table th {
        font-weight: 600;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }

    /* Botones */
    .btn {
        font-weight: 500;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        transition: all 0.2s;
    }

    .btn:hover {
        transform: translateY(-1px);
    }

    .btn-sm {
        padding: 0.25rem 0.75rem;
    }

    /* Enlaces */
    a {
        color: inherit;
        text-decoration: none;
    }

    a:hover {
        color: #0d6efd;
    }

    /* Estados hover */
    .table tbody tr {
        transition: background-color 0.2s;
    }

    .table tbody tr:hover {
        background-color: rgba(0,0,0,0.01);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .container {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .avatar-circle {
            width: 32px;
            height: 32px;
        }

        .table > :not(caption) > * > * {
            padding: 0.75rem 1rem;
        }

        .d-flex.justify-content-center {
            flex-direction: column;
        }

        .btn {
            width: 100%;
            margin-bottom: 0.5rem;
        }
    }
</style>
@endpush
