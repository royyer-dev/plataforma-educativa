@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <h1><i class="fas fa-user-check me-2"></i>Gestión de Solicitudes de Inscripción</h1>
        <a href="{{ route('docente.dashboard') }}" class="btn btn-sm btn-outline-secondary mt-2 mt-md-0">
            <i class="fas fa-arrow-left me-1"></i> Volver al Dashboard
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
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="fas fa-hourglass-half me-2"></i>Solicitudes Pendientes</h5>
        </div>
        <div class="card-body p-0"> {{-- p-0 para que la tabla se ajuste al card --}}
            @if(isset($solicitudesPendientes) && $solicitudesPendientes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0"> {{-- mb-0 si es el último elemento del card-body --}}
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Fecha Solicitud</th>
                                <th scope="col">Estudiante</th>
                                <th scope="col">Correo Electrónico</th>
                                <th scope="col">Curso Solicitado</th>
                                <th scope="col">Carrera</th> {{-- Nueva Columna --}}
                                <th scope="col" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($solicitudesPendientes as $solicitud)
                                <tr>
                                    <td>{{ $solicitud->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ optional($solicitud->estudiante)->nombre }} {{ optional($solicitud->estudiante)->apellidos }}</td>
                                    <td>{{ optional($solicitud->estudiante)->email }}</td>
                                    <td>
                                        <a href="{{ route('docente.cursos.show', optional($solicitud->curso)->id) }}" class="text-decoration-none" title="Ver detalles del curso">
                                            {{ Str::limit(optional($solicitud->curso)->titulo, 30) }}
                                        </a>
                                    </td>
                                    <td>{{ optional(optional($solicitud->curso)->carrera)->nombre ?? 'N/A' }}</td>
                                    <td class="text-center text-nowrap">
                                        <form action="{{ route('docente.solicitudes.aprobar', $solicitud->id) }}" method="POST" class="d-inline-block me-1">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-success btn-sm" title="Aprobar Solicitud">
                                                <i class="fas fa-check"></i> Aprobar
                                            </button>
                                        </form>
                                        <form action="{{ route('docente.solicitudes.rechazar', $solicitud->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('¿Estás seguro de querer rechazar esta solicitud?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Rechazar Solicitud">
                                                <i class="fas fa-times"></i> Rechazar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="card-body text-center text-muted">
                    <p class="mb-0 py-3"><i class="fas fa-info-circle me-1"></i>No hay solicitudes de inscripción pendientes en este momento.</p>
                </div>
            @endif
        </div>
        @if(isset($solicitudesPendientes) && $solicitudesPendientes->hasPages())
            <div class="card-footer bg-light border-top-0">
                {{-- Asegurar que los parámetros de paginación no colisionen --}}
                {{ $solicitudesPendientes->appends(request()->except('pendientes_page'))->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>


    {{-- Sección Historial de Solicitudes Aceptadas --}}
    <div class="card shadow-sm">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-history me-2"></i>Historial de Inscripciones Aceptadas</h5>
        </div>
        <div class="card-body p-0">
            @if(isset($solicitudesAceptadas) && $solicitudesAceptadas->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Fecha Aprobación</th>
                                <th scope="col">Estudiante</th>
                                <th scope="col">Correo Electrónico</th>
                                <th scope="col">Curso Inscrito</th>
                                <th scope="col">Carrera</th> {{-- Nueva Columna --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($solicitudesAceptadas as $solicitud)
                                <tr>
                                    <td>{{ $solicitud->fecha_inscripcion ? Carbon\Carbon::parse($solicitud->fecha_inscripcion)->format('d/m/Y H:i') : $solicitud->updated_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ optional($solicitud->estudiante)->nombre }} {{ optional($solicitud->estudiante)->apellidos }}</td>
                                    <td>{{ optional($solicitud->estudiante)->email }}</td>
                                    <td>
                                        <a href="{{ route('docente.cursos.show', optional($solicitud->curso)->id) }}" class="text-decoration-none" title="Ver detalles del curso">
                                            {{ Str::limit(optional($solicitud->curso)->titulo, 30) }}
                                        </a>
                                    </td>
                                    <td>{{ optional(optional($solicitud->curso)->carrera)->nombre ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="card-body text-center text-muted">
                     <p class="mb-0 py-3"><i class="fas fa-info-circle me-1"></i>Aún no has aceptado ninguna solicitud de inscripción.</p>
                </div>
            @endif
        </div>
        @if(isset($solicitudesAceptadas) && $solicitudesAceptadas->hasPages())
            <div class="card-footer bg-light border-top-0">
                {{ $solicitudesAceptadas->appends(request()->except('aceptadas_page'))->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

</div>
@endsection
