@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Todos Mis Estudiantes Inscritos</h1>
        {{-- Podrías añadir un botón para volver al dashboard si quieres --}}
        <a href="{{ route('docente.dashboard') }}" class="btn btn-sm btn-outline-secondary">Volver al Dashboard</a>
    </div>
    <p>Aquí se listan todos los estudiantes con inscripciones activas en los cursos que impartes.</p>

    {{-- Formulario de Búsqueda --}}
    <form method="GET" action="{{ route('docente.estudiantes.generales') }}" class="mb-4">
        <div class="input-group">
            <input type="text" class="form-control" name="busqueda" placeholder="Buscar por nombre, apellido o email..." value="{{ $terminoBusqueda ?? '' }}">
            <button class="btn btn-outline-primary" type="submit"><i class="fas fa-search"></i> Buscar</button>
            @if(isset($terminoBusqueda) && $terminoBusqueda)
                <a href="{{ route('docente.estudiantes.generales') }}" class="btn btn-outline-secondary">Limpiar</a>
            @endif
        </div>
    </form>

    @if($inscripciones && $inscripciones->count() > 0)
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Estudiante</th>
                        <th>Correo Electrónico</th>
                        <th>Curso Inscrito</th>
                        <th>Fecha Inscripción</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($inscripciones as $inscripcion)
                        <tr>
                            <td>{{ optional($inscripcion->estudiante)->apellidos }}, {{ optional($inscripcion->estudiante)->nombre }}</td>
                            <td>{{ optional($inscripcion->estudiante)->email }}</td>
                            <td>
                                <a href="{{ route('docente.cursos.show', optional($inscripcion->curso)->id) }}">
                                    {{ optional($inscripcion->curso)->titulo }}
                                </a>
                            </td>
                            <td>{{ $inscripcion->fecha_inscripcion ? $inscripcion->fecha_inscripcion->format('d/m/Y') : 'N/A' }}</td>
                            <td class="text-center text-nowrap">
                                {{-- Enlace a la vista de detalles del estudiante DENTRO de ese curso --}}
                                <a href="{{ route('docente.cursos.estudiantes.show', [optional($inscripcion->curso)->id, optional($inscripcion->estudiante)->id]) }}" class="btn btn-info btn-sm" title="Ver progreso en este curso">
                                    <i class="fas fa-eye"></i> Progreso
                                </a>
                                {{-- Podrías añadir un botón para dar de baja desde aquí también si es útil --}}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        <div class="mt-3">
            {{ $inscripciones->appends(['busqueda' => $terminoBusqueda])->links() }} {{-- Añadir término de búsqueda a la paginación --}}
        </div>
    @else
        @if(isset($terminoBusqueda) && $terminoBusqueda)
            <div class="alert alert-warning">No se encontraron estudiantes que coincidan con "<strong>{{ $terminoBusqueda }}</strong>".</div>
        @else
            <div class="alert alert-info">No tienes estudiantes con inscripciones activas en tus cursos.</div>
        @endif
    @endif
</div>
@endsection
