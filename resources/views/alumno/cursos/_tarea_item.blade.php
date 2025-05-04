{{-- Recibe una variable $tarea y $curso (necesitamos el curso para la ruta) --}}
@php
    // Asegurarse que $curso esté disponible. Si no se pasa, intentar obtenerlo de la tarea.
    // Es mejor pasarlo explícitamente al incluir el partial: @include('...', ['tarea' => $tarea, 'curso' => $curso])
    $cursoDeTarea = $curso ?? $tarea->curso; // Usa $curso si está disponible, si no, intenta $tarea->curso
@endphp

{{-- Verifica si $cursoDeTarea se pudo obtener antes de intentar usarlo en la ruta --}}
@if($cursoDeTarea)
    <div class="mb-3 p-2 border rounded">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-clipboard-list fa-fw"></i> {{-- Icono de tarea (opcional, requiere Font Awesome) --}}
            {{-- vvv Título ahora es un enlace vvv --}}
            <strong>
                {{-- Enlace a la vista de detalles de la tarea específica --}}
                <a href="{{ route('alumno.cursos.tareas.show', [$cursoDeTarea->id, $tarea->id]) }}">
                    {{ $tarea->titulo }}
                </a>
            </strong>
            {{-- ^^^ Fin Enlace ^^^ --}}
        </div>
        @if($tarea->descripcion)
            {{-- Mostramos solo una parte aquí, los detalles completos están en la página de la tarea --}}
            <p class="mb-1 small text-muted">{{ Str::limit($tarea->descripcion, 150) }}</p>
        @endif
        <p class="mb-0 small">
            <strong>Límite:</strong> {{ $tarea->fecha_limite ? $tarea->fecha_limite->format('d/m/Y H:i') : 'Sin límite' }}
            {{-- Mostrar información sobre entrega tardía --}}
            @if($tarea->permite_entrega_tardia && $tarea->fecha_limite_tardia)
                (Tardía hasta: {{ $tarea->fecha_limite_tardia->format('d/m/Y H:i') }})
            @elseif($tarea->permite_entrega_tardia)
                (Permite tardía)
            @endif
            | <strong>Puntos:</strong> {{ $tarea->puntos_maximos ?? 'N/A' }}
            | <strong>Tipo:</strong> {{ ucfirst($tarea->tipo_entrega) }}
        </p>
        {{-- Se quita el botón "Ver Tarea" ya que el título es el enlace --}}
    </div>
@else
    {{-- Mensaje de fallback si no se pudo determinar el curso (poco probable si se incluye correctamente) --}}
    <div class="mb-3 p-2 border rounded bg-light text-danger">
        <small>Error: No se pudo generar el enlace para la tarea '{{ $tarea->titulo }}'. Falta información del curso.</small>
    </div>
@endif
