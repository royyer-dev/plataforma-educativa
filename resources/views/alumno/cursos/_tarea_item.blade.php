{{-- Recibe una variable $tarea y $curso (necesitamos el curso para la ruta) --}}
@php
    // Asegurarse que $cursoDeTarea esté disponible.
    // Es mejor pasarlo explícitamente al incluir el partial: @include('...', ['tarea' => $tarea, 'curso' => $curso])
    $cursoDeTarea = $curso ?? $tarea->curso; // Usa $curso si está disponible, si no, intenta $tarea->curso
@endphp

{{-- Verifica si $cursoDeTarea se pudo obtener antes de intentar usarlo en la ruta --}}
@if($cursoDeTarea)
    <div class="list-group-item list-group-item-action py-3 px-0 mb-2 border-bottom"> {{-- Estilo de item de lista, padding y borde --}}
        <div class="d-flex w-100 justify-content-between align-items-center">
            <div class="flex-grow-1">
                <h6 class="mb-1">
                    <a href="{{ route('alumno.cursos.tareas.show', [$cursoDeTarea->id, $tarea->id]) }}" class="text-decoration-none fw-bold">
                        <i class="fas fa-clipboard-list fa-fw me-2 text-primary"></i>{{ $tarea->titulo }}
                    </a>
                </h6>
                @if($tarea->descripcion)
                    <p class="mb-1 small text-muted">{{ Str::limit($tarea->descripcion, 120) }}</p>
                @endif
                <div class="d-flex flex-wrap small text-muted mt-1">
                    <span class="me-3" title="Fecha Límite">
                        <i class="fas fa-calendar-alt fa-fw me-1"></i>
                        <strong>Límite:</strong> {{ $tarea->fecha_limite ? $tarea->fecha_limite->format('d/m/Y H:i') : 'Sin límite' }}
                        @if($tarea->permite_entrega_tardia)
                            <span class="badge bg-warning-light text-warning-dark ms-1" style="font-size: 0.75em;">
                                <i class="fas fa-exclamation-triangle fa-fw"></i>
                                @if($tarea->fecha_limite_tardia)
                                    Tardía hasta: {{ $tarea->fecha_limite_tardia->format('d/m/Y') }}
                                @else
                                    Permite tardía
                                @endif
                            </span>
                        @endif
                    </span>
                    <span class="me-3" title="Puntos Máximos">
                        <i class="fas fa-star fa-fw me-1"></i>
                        <strong>Puntos:</strong> {{ $tarea->puntos_maximos ?? 'N/A' }}
                    </span>
                    <span title="Tipo de Entrega">
                        <i class="fas fa-file-import fa-fw me-1"></i>
                        <strong>Tipo:</strong> {{ ucfirst($tarea->tipo_entrega) }}
                    </span>
                </div>
            </div>
            <div class="ms-2"> {{-- Contenedor para el chevron --}}
                <i class="fas fa-chevron-right text-muted"></i>
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
</style>
