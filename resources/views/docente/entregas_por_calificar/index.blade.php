@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="display-5 mb-0">Entregas Pendientes</h1>
            <p class="text-muted mb-0">Gestiona las entregas que necesitan calificación</p>
        </div>
        <a href="{{ route('docente.dashboard') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-2"></i>Dashboard
        </a>
    </div>

    {{-- Formulario de Búsqueda --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('docente.entregas.porCalificar') }}">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" 
                           class="form-control border-start-0 ps-0" 
                           name="busqueda" 
                           placeholder="Buscar por estudiante o tarea..." 
                           value="{{ $terminoBusqueda ?? '' }}"
                           aria-label="Término de búsqueda">
                    <button class="btn btn-primary px-4" type="submit">Buscar</button>
                    @if(isset($terminoBusqueda) && $terminoBusqueda)
                        <a href="{{ route('docente.entregas.porCalificar') }}" class="btn btn-light">
                            <i class="fas fa-times me-1"></i>Limpiar
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @if($entregasPorCalificar && $entregasPorCalificar->count() > 0)
        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0">Curso</th>
                            <th class="border-0">Tarea</th>
                            <th class="border-0">Estudiante</th>
                            <th class="border-0">Fecha Entrega</th>
                            <th class="border-0">Estado</th>
                            <th class="border-0 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($entregasPorCalificar as $entrega)
                            <tr>
                                <td>
                                    <a href="{{ route('docente.cursos.show', optional(optional($entrega->tarea)->curso)->id) }}" 
                                       class="text-decoration-none">
                                        {{ Str::limit(optional(optional($entrega->tarea)->curso)->titulo, 25) }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('docente.cursos.tareas.entregas.index', [optional(optional($entrega->tarea)->curso)->id, optional($entrega->tarea)->id]) }}" 
                                       class="text-decoration-none">
                                        {{ Str::limit(optional($entrega->tarea)->titulo, 30) }}
                                    </a>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle bg-primary text-white me-2">
                                            {{ substr(optional($entrega->estudiante)->nombre, 0, 1) }}
                                        </div>
                                        <div>
                                            {{ optional($entrega->estudiante)->nombre }} {{ optional($entrega->estudiante)->apellidos }}
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="far fa-clock text-muted me-2"></i>
                                        {{ $entrega->created_at->format('d/m/Y H:i') }}
                                    </div>
                                </td>
                                <td>
                                    @if($entrega->estado_entrega == 'entregado')
                                        <span class="badge bg-primary">Entregado</span>
                                    @elseif($entrega->estado_entrega == 'entregado_tarde')
                                        <span class="badge bg-warning text-dark">Entregado Tarde</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($entrega->estado_entrega) }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('docente.cursos.tareas.entregas.calificar.form', [optional(optional($entrega->tarea)->curso)->id, optional($entrega->tarea)->id, $entrega->id]) }}" 
                                       class="btn btn-primary btn-sm">
                                        <i class="fas fa-check-circle me-1"></i> Calificar
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($entregasPorCalificar->hasPages())
                <div class="card-footer bg-white border-top-0 pt-0">
                    {{ $entregasPorCalificar->appends(['busqueda' => $terminoBusqueda])->links() }}
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
                        <p>No se encontraron entregas pendientes que coincidan con "<strong>{{ $terminoBusqueda }}</strong>".</p>
                    </div>
                @else
                    <div class="text-success">
                        <i class="fas fa-check-circle fa-3x mb-3"></i>
                        <h5>¡Todo al día!</h5>
                        <p class="mb-0">No tienes entregas pendientes de calificación.</p>
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
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 500;
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
    }

    .table td {
        vertical-align: middle;
    }

    /* Mejoras en los badges */
    .badge {
        padding: 0.5em 0.75em;
        font-weight: 500;
    }

    /* Mejoras en los botones */
    .btn {
        font-weight: 500;
        padding: 0.5rem 1rem;
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

    /* Animaciones hover */
    .table tbody tr {
        transition: background-color 0.2s;
    }

    .table tbody tr:hover {
        background-color: rgba(0,0,0,0.01);
    }

    .btn {
        transition: all 0.2s;
    }

    .btn:hover {
        transform: translateY(-1px);
    }
</style>
@endpush
