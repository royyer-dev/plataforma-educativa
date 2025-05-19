@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Entregas Pendientes de Calificación</h1>
        <a href="{{ route('docente.dashboard') }}" class="btn btn-sm btn-outline-secondary">Volver al Dashboard</a>
    </div>
    <p>Aquí se listan todas las entregas de tareas de tus cursos que aún no has calificado.</p>

    {{-- Formulario de Búsqueda --}}
    <form method="GET" action="{{ route('docente.entregas.porCalificar') }}" class="mb-4">
        <div class="input-group">
            <input type="text" class="form-control" name="busqueda" placeholder="Buscar por estudiante o tarea..." value="{{ $terminoBusqueda ?? '' }}">
            <button class="btn btn-outline-primary" type="submit"><i class="fas fa-search"></i> Buscar</button>
            @if(isset($terminoBusqueda) && $terminoBusqueda)
                <a href="{{ route('docente.entregas.porCalificar') }}" class="btn btn-outline-secondary">Limpiar</a>
            @endif
        </div>
    </form>

    @if($entregasPorCalificar && $entregasPorCalificar->count() > 0)
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Curso</th>
                        <th>Tarea</th>
                        <th>Estudiante</th>
                        <th>Fecha Entrega</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($entregasPorCalificar as $entrega)
                        <tr>
                            <td>
                                <a href="{{ route('docente.cursos.show', optional(optional($entrega->tarea)->curso)->id) }}">
                                    {{ Str::limit(optional(optional($entrega->tarea)->curso)->titulo, 25) }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('docente.cursos.tareas.entregas.index', [optional(optional($entrega->tarea)->curso)->id, optional($entrega->tarea)->id]) }}">
                                    {{ Str::limit(optional($entrega->tarea)->titulo, 30) }}
                                </a>
                            </td>
                            <td>{{ optional($entrega->estudiante)->nombre }} {{ optional($entrega->estudiante)->apellidos }}</td>
                            <td>{{ $entrega->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                @if($entrega->estado_entrega == 'entregado')
                                    <span class="badge bg-primary">Entregado</span>
                                @elseif($entrega->estado_entrega == 'entregado_tarde')
                                    <span class="badge bg-warning text-dark">Entregado Tarde</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($entrega->estado_entrega) }}</span>
                                @endif
                            </td>
                            <td class="text-center text-nowrap">
                                <a href="{{ route('docente.cursos.tareas.entregas.calificar.form', [optional(optional($entrega->tarea)->curso)->id, optional($entrega->tarea)->id, $entrega->id]) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-edit"></i> Calificar
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        <div class="mt-3">
            {{ $entregasPorCalificar->appends(['busqueda' => $terminoBusqueda])->links() }}
        </div>
    @else
        @if(isset($terminoBusqueda) && $terminoBusqueda)
            <div class="alert alert-warning">No se encontraron entregas pendientes que coincidan con "<strong>{{ $terminoBusqueda }}</strong>".</div>
        @else
            <div class="alert alert-info">¡Excelente! No tienes entregas pendientes de calificación.</div>
        @endif
    @endif
</div>
@endsection
