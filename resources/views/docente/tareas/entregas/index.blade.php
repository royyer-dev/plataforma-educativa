@extends('layouts.app')

@section('content')
<div class="container">
    {{-- Navegación para volver a los detalles del curso --}}
    <div class="mb-3">
        <a href="{{ route('docente.cursos.show', $curso->id) }}" class="btn btn-secondary btn-sm">&laquo; Volver al Curso: {{ $curso->titulo }}</a>
    </div>

    {{-- Título indicando la tarea --}}
    <h1>Entregas para la Tarea: {{ $tarea->titulo }}</h1>
    <p>Total de entregas: {{ $entregas->total() }}</p> {{-- Muestra el total de entregas --}}
    <hr>

    {{-- Mensajes de estado (ej: después de calificar) --}}
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


    @if($entregas && $entregas->count() > 0)
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th>Estudiante</th>
                        <th>Fecha Entrega</th>
                        <th>Estado</th>
                        <th>Contenido Entregado</th>
                        <th>Calificación</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Itera sobre las entregas pasadas desde el controlador --}}
                    @foreach ($entregas as $entrega)
                        <tr>
                            {{-- Nombre del estudiante --}}
                            <td>{{ optional($entrega->estudiante)->nombre }} {{ optional($entrega->estudiante)->apellidos }}</td>

                            {{-- Fecha y hora de entrega --}}
                            <td>{{ $entrega->fecha_entrega->format('d/m/Y H:i') }}</td>

                            {{-- Estado de la entrega (con badge) --}}
                            <td>
                                @if($entrega->estado_entrega == 'entregado')
                                    <span class="badge bg-primary">Entregado</span>
                                @elseif($entrega->estado_entrega == 'entregado_tarde')
                                    <span class="badge bg-warning text-dark">Entregado Tarde</span>
                                @elseif($entrega->estado_entrega == 'calificado')
                                    <span class="badge bg-success">Calificado</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($entrega->estado_entrega) }}</span>
                                @endif
                            </td>

                            {{-- Enlace/Texto del contenido entregado --}}
                            <td>
                                @if($entrega->ruta_archivo)
                                    {{-- Enlace al archivo (requiere storage:link) --}}
                                    <a href="{{ Storage::url($entrega->ruta_archivo) }}" target="_blank">Ver Archivo</a>
                                @elseif($entrega->texto_entrega)
                                    {{-- Botón para abrir modal con el texto --}}
                                    <button type="button" class="btn btn-link btn-sm p-0" data-bs-toggle="modal" data-bs-target="#textoEntregaModal{{ $entrega->id }}">
                                        Ver Texto
                                    </button>
                                    {{-- Modal (se define uno por cada entrega) --}}
                                    <div class="modal fade" id="textoEntregaModal{{ $entrega->id }}" tabindex="-1" aria-labelledby="textoEntregaModalLabel{{ $entrega->id }}" aria-hidden="true">
                                      <div class="modal-dialog modal-lg modal-dialog-scrollable"> {{-- modal-lg y scrollable --}}
                                        <div class="modal-content">
                                          <div class="modal-header">
                                            <h5 class="modal-title" id="textoEntregaModalLabel{{ $entrega->id }}">Texto Entregado por {{ optional($entrega->estudiante)->nombre }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                          </div>
                                          <div class="modal-body">
                                            {{-- Usamos <pre> para mantener formato --}}
                                            <pre style="white-space: pre-wrap; word-wrap: break-word;">{{ $entrega->texto_entrega }}</pre>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                @elseif($entrega->url_entrega)
                                    {{-- Enlace a la URL entregada --}}
                                    <a href="{{ $entrega->url_entrega }}" target="_blank" rel="noopener noreferrer">Ver Enlace</a>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>

                            {{-- Calificación actual --}}
                            <td>
                                {{-- Muestra la calificación si existe, si no '--' --}}
                                {{ $entrega->calificacion ?? '--' }} / {{ $tarea->puntos_maximos ?? 'N/A' }}
                            </td>

                            {{-- Acciones (Calificar) --}}
                            <td class="text-center text-nowrap">
                                {{-- vvv Botón Calificar Funcional vvv --}}
                                {{-- Enlace a la ruta que muestra el formulario de calificación --}}
                                <a href="{{ route('docente.cursos.tareas.entregas.calificar.form', [$curso->id, $tarea->id, $entrega->id]) }}" class="btn btn-primary btn-sm">
                                    {{ $entrega->calificacion !== null ? 'Ver/Editar Calificación' : 'Calificar' }} {{-- Cambia texto si ya está calificado --}}
                                </a>
                                {{-- ^^^ Fin Botón Calificar ^^^ --}}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        <div class="mt-3">
            {{ $entregas->links() }}
        </div>

    @else
        <div class="alert alert-info">
            Aún no hay entregas para esta tarea.
        </div>
    @endif
</div>
@endsection
