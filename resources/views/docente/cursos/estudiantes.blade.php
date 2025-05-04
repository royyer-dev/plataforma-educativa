@extends('layouts.app') {{-- Hereda el layout principal --}}

@section('content')
<div class="container">
    {{-- Navegación para volver a los detalles del curso --}}
    <div class="mb-3">
        <a href="{{ route('docente.cursos.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> {{-- Icono opcional --}}
            Volver a Cursos
        </a>
    </div>

    {{-- Título de la página --}}
    <h1>Estudiantes Inscritos en: {{ $curso->titulo }}</h1>
    <p>A continuación se muestra la lista de estudiantes actualmente activos en este curso.</p>
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

    {{-- Verificar si hay estudiantes inscritos --}}
    @if($estudiantesInscritos && $estudiantesInscritos->count() > 0)
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Apellidos</th>
                        <th>Nombre(s)</th>
                        <th>Correo Electrónico</th>
                        <th>Fecha Inscripción</th>
                        {{-- <th>Promedio General</th> --}} {{-- Placeholder --}}
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Iterar sobre la colección de estudiantes activos --}}
                    @foreach ($estudiantesInscritos as $estudiante)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $estudiante->apellidos ?? '--' }}</td>
                            <td>{{ $estudiante->nombre }}</td>
                            <td>{{ $estudiante->email }}</td>
                            {{-- Formatear fecha usando Carbon::parse --}}
                            <td>
                                @if(optional($estudiante->pivot)->fecha_inscripcion)
                                    {{ \Carbon\Carbon::parse(optional($estudiante->pivot)->fecha_inscripcion)->format('d/m/Y') }}
                                @else
                                    N/A
                                @endif
                            </td>
                            {{-- <td> -- Placeholder -- </td> --}}
                            <td class="text-center text-nowrap">
                                {{-- vvv BOTÓN MODIFICADO vvv --}}
                                {{-- Enlace a la nueva ruta para ver detalles del estudiante en este curso --}}
                                <a href="{{ route('docente.cursos.estudiantes.show', [$curso->id, $estudiante->id]) }}" class="btn btn-info btn-sm">Ver Detalles</a>
                                {{-- ^^^ FIN BOTÓN MODIFICADO ^^^ --}}
                                {{-- Aquí podríamos añadir el botón "Dar de Baja" funcional más adelante --}}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Paginación (si aplica) --}}
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
