@extends('layouts.app')

@push('styles')
<style>
    /* Contenedor principal */
    .container {
        max-width: 1200px;
        padding: 2rem 1rem;
    }

    /* Estilos para el encabezado */
    h1 {
        color: #2c3e50;
        font-weight: 700;
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }
    .page-description {
        color: #6c757d;
        font-size: 1.1rem;
        margin-bottom: 1.5rem;
    }

    /* Botón de navegación */
    .btn-secondary {
        background-color: #f8f9fa;
        color: #2c3e50;
        border: none;
        padding: 0.5rem 1rem;
        transition: all 0.2s ease;
        font-weight: 500;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .btn-secondary:hover {
        background-color: #e9ecef;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    /* Tabla de estudiantes */
    .table-container {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    .table {
        margin-bottom: 0;
    }
    .table thead {
        background-color: #f8f9fa;
    }
    .table thead th {
        font-weight: 600;
        color: #2c3e50;
        padding: 1rem;
        border-bottom: 2px solid #e9ecef;
    }
    .table tbody tr {
        transition: all 0.2s ease;
    }
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }
    .table td {
        padding: 1rem;
        vertical-align: middle;
    }

    /* Botones de acción */
    .btn-info {
        background-color: #0dcaf0;
        border: none;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        transition: all 0.2s ease;
    }
    .btn-info:hover {
        background-color: #0bbed4;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(13,202,240,0.2);
    }

    /* Estado vacío */
    .alert-info {
        background-color: #f8f9fa;
        border: none;
        color: #2c3e50;
        border-radius: 1rem;
        padding: 2rem;
        text-align: center;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
    }

    /* Estilos para el mensaje de estado */
    .alert-success, .alert-danger {
        border: none;
        border-radius: 0.5rem;
        padding: 1rem 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .alert-success {
        background-color: #d1e7dd;
        color: #0f5132;
    }
    .alert-danger {
        background-color: #f8d7da;
        color: #842029;
    }

    /* Separador */
    hr {
        border: none;
        height: 2px;
        background: linear-gradient(to right, #e9ecef, transparent);
        margin: 1.5rem 0;
    }

    /* Paginación */
    .pagination {
        margin-top: 1.5rem;
        justify-content: center;
    }
    .page-link {
        border: none;
        padding: 0.5rem 1rem;
        margin: 0 0.25rem;
        border-radius: 0.5rem;
        color: #2c3e50;
        transition: all 0.2s ease;
    }
    .page-link:hover {
        background-color: #e9ecef;
        transform: translateY(-1px);
    }
    .page-item.active .page-link {
        background-color: #0dcaf0;
        color: white;
    }
</style>
@endpush

@section('content')
<div class="container">
    {{-- Navegación para volver a los detalles del curso --}}
    <div class="mb-3">
        <a href="{{ route('docente.cursos.show', $curso->id) }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> {{-- Icono opcional --}}
            Volver a Detalles del Curso
        </a>
    </div>    {{-- Título de la página --}}
    <h1 class="mb-2">Estudiantes Inscritos en: {{ $curso->titulo }}</h1>
    <p class="page-description">A continuación se muestra la lista de estudiantes actualmente activos en este curso.</p>
    <hr>

    {{-- Mensajes de estado --}}
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
     @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Verificar si hay estudiantes inscritos --}}    @if($estudiantesInscritos && $estudiantesInscritos->count() > 0)
        <div class="table-container">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Apellidos</th>
                        <th>Nombre(s)</th>
                        <th>Correo Electrónico</th>
                        <th>Fecha Inscripción</th>
                        {{-- <th>Promedio General</th> --}} {{-- Placeholder para futuro --}}
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Iterar sobre la colección de estudiantes activos --}}
                    @foreach ($estudiantesInscritos as $estudiante_item) {{-- Usar una variable de bucle diferente --}}
                        <tr>
                            <td>{{ $loop->iteration }}</td> {{-- Número de fila --}}
                            <td>{{ $estudiante_item->apellidos ?? '--' }}</td>
                            <td>{{ $estudiante_item->nombre }}</td>
                            <td>{{ $estudiante_item->email }}</td>
                            {{-- Acceder a la fecha desde la tabla pivote 'inscripciones' --}}
                            <td>
                                @if(optional($estudiante_item->pivot)->fecha_inscripcion)
                                    {{ \Carbon\Carbon::parse(optional($estudiante_item->pivot)->fecha_inscripcion)->format('d/m/Y') }}
                                @else
                                    N/A
                                @endif
                            </td>
                            {{-- <td> -- Placeholder -- </td> --}}
                            <td class="text-center text-nowrap">
                                {{-- Enlace a la ruta para ver detalles del estudiante en este curso --}}
                                <a href="{{ route('docente.cursos.estudiantes.show', [$curso->id, $estudiante_item->id]) }}" class="btn btn-info btn-sm">Ver Detalles</a>

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Si usaste paginación en el controlador, añádela aquí --}}
        {{-- <div class="mt-3">
            {{ $estudiantesInscritos->links() }}
        </div> --}}

    @else
        {{-- Mensaje si no hay estudiantes activos --}}
        <div class="alert alert-info">
            No hay estudiantes activos inscritos en este curso actualmente.
        </div>
    @endif

</div>
@endsection
