@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Solicitudes de Inscripción Pendientes</h1>

    {{-- Mensajes de estado (si apruebas/rechazas) --}}
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


    @if($solicitudesPendientes && $solicitudesPendientes->count() > 0)
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle"> {{-- align-middle para centrar verticalmente --}}
                <thead>
                    <tr>
                        <th>Fecha Solicitud</th>
                        <th>Estudiante</th>
                        <th>Correo Electrónico</th>
                        <th>Curso Solicitado</th>
                        <th class="text-center">Acciones</th> {{-- Centrar acciones --}}
                    </tr>
                </thead>
                <tbody>
                    {{-- Itera sobre las solicitudes pasadas desde el controlador --}}
                    @foreach ($solicitudesPendientes as $solicitud)
                        <tr>
                            <td>{{ $solicitud->created_at->format('d/m/Y H:i') }}</td>
                            {{-- Accede a los datos del estudiante a través de la relación --}}
                            <td>{{ optional($solicitud->estudiante)->nombre }} {{ optional($solicitud->estudiante)->apellidos }}</td>
                            <td>{{ optional($solicitud->estudiante)->email }}</td>
                            {{-- Accede a los datos del curso a través de la relación --}}
                            <td>{{ optional($solicitud->curso)->titulo }}</td>
                            <td class="text-center text-nowrap"> {{-- Centrar y evitar wrap --}}
                                {{-- vvv INICIO: Formularios Funcionales para Aprobar/Rechazar vvv --}}

                                {{-- Formulario para Aprobar (Usa PATCH o POST según tu ruta) --}}
                                <form action="{{ route('docente.solicitudes.aprobar', $solicitud->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('PATCH') {{-- Cambia a POST si definiste la ruta con POST --}}
                                    <button type="submit" class="btn btn-success btn-sm">Aprobar</button>
                                </form>

                                {{-- Formulario para Rechazar (Usa DELETE o POST según tu ruta) --}}
                                <form action="{{ route('docente.solicitudes.rechazar', $solicitud->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('¿Estás seguro de querer rechazar esta solicitud?')">
                                    @csrf
                                    @method('DELETE') {{-- Cambia a POST si definiste la ruta con POST --}}
                                    <button type="submit" class="btn btn-danger btn-sm">Rechazar</button>
                                </form>
                                {{-- ^^^ FIN: Formularios Funcionales ^^^ --}}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        <div class="mt-3">
            {{ $solicitudesPendientes->links() }}
        </div>

    @else
        <div class="alert alert-info">
            No hay solicitudes de inscripción pendientes en este momento.
        </div>
    @endif
</div>
@endsection
