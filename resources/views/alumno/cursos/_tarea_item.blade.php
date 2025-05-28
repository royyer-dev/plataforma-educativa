{{-- Recibe una variable $tarea y $curso (necesitamos el curso para la ruta) --}}
@php
    // Asegurarse que $cursoDeTarea esté disponible.
    // Es mejor pasarlo explícitamente al incluir el partial: @include('...', ['tarea' => $tarea, 'curso' => $curso])
    $cursoDeTarea = $curso ?? $tarea->curso; // Usa $curso si está disponible, si no, intenta $tarea->curso
@endphp

{{-- Verifica si $cursoDeTarea se pudo obtener antes de intentar usarlo en la ruta --}}
@if($cursoDeTarea)
    <div class="card shadow-sm hover-shadow-sm mb-3">
        <div class="card-body">
            <div class="d-flex align-items-start gap-3">
                {{-- Estado de la tarea con indicador visual --}}
                <div class="task-status-indicator">
                    @if($tarea->estado == 'pendiente')
                        <div class="status-circle bg-warning" title="Pendiente"></div>
                    @elseif($tarea->estado == 'entregada')
                        <div class="status-circle bg-info" title="Entregada"></div>
                    @elseif($tarea->estado == 'calificada')
                        <div class="status-circle bg-success" title="Calificada"></div>
                    @elseif($tarea->estado == 'vencida')
                        <div class="status-circle bg-danger" title="Vencida"></div>
                    @endif
                </div>

                <div class="flex-grow-1">
                    {{-- Encabezado de la tarea --}}
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="card-title mb-0">{{ $tarea->titulo }}</h5>
                        <span class="badge bg-primary rounded-pill">
                            {{ number_format($tarea->puntaje_maximo, 0) }} pts
                        </span>
                    </div>

                    {{-- Descripción --}}
                    @if($tarea->descripcion)
                        <p class="card-text text-muted mb-3">
                            {{ Str::limit($tarea->descripcion, 150) }}
                        </p>
                    @endif

                    {{-- Detalles y fechas --}}
                    <div class="d-flex flex-wrap gap-3 mb-3">
                        <div class="small text-muted">
                            <i class="fas fa-clock me-1"></i>
                            Fecha límite: {{ $tarea->fecha_limite->format('d/m/Y H:i') }}
                        </div>
                        @if($tarea->estado == 'calificada')
                            <div class="small text-success">
                                <i class="fas fa-star me-1"></i>
                                Calificación: {{ number_format($tarea->calificacion, 1) }}/{{ number_format($tarea->puntaje_maximo, 1) }}
                            </div>
                        @endif
                    </div>

                    {{-- Botones de acción --}}
                    <div class="d-flex gap-2">
                        <a href="{{ route('alumno.tareas.show', $tarea->id) }}" 
                           class="btn btn-sm btn-primary">
                            <i class="fas fa-eye me-1"></i>Ver Detalles
                        </a>
                        @if($tarea->estado == 'pendiente')
                            <a href="{{ route('alumno.tareas.entregar', $tarea->id) }}" 
                               class="btn btn-sm btn-success">
                                <i class="fas fa-upload me-1"></i>Entregar
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
    {{-- Mensaje de fallback si no se pudo determinar el curso --}}
    <div class="list-group-item list-group-item-action py-3 px-0 mb-2 border-bottom bg-light">
        <div class="d-flex align-items-center gap-2 text-danger">
            <i class="fas fa-exclamation-triangle fa-fw"></i>
            <small>Error: No se pudo generar el enlace para la tarea '{{ $tarea->titulo }}'. Falta información del curso.</small>
        </div>
    </div>
@endif

{{-- Estilo para el badge de entrega tardía (puedes moverlo a tu app.scss) --}}
<style>
    .badge.bg-warning-light {
        color: #664d03; /* Color de texto Bootstrap para warning */
        background-color: #fff3cd; /* Color de fondo Bootstrap para warning claro */
        border: 1px solid #ffecb5;
    }
    .task-status-indicator {
        padding-top: 5px;
    }
    .status-circle {
        width: 12px;
        height: 12px;
        border-radius: 50%;
    }
    .hover-shadow-sm {
        transition: all 0.2s ease-in-out;
    }
    .hover-shadow-sm:hover {
        transform: translateY(-1px);
        box-shadow: 0 .125rem .25rem rgba(0,0,0,.075)!important;
    }
</style>
