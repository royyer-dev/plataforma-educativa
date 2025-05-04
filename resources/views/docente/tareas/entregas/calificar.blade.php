@extends('layouts.app')

@section('content')
<div class="container">
    {{-- Navegación para volver a la lista de entregas --}}
    <div class="mb-3">
        <a href="{{ route('docente.cursos.tareas.entregas.index', [$curso->id, $tarea->id]) }}" class="btn btn-secondary btn-sm">&laquo; Volver a Entregas de Tarea</a>
    </div>

    {{-- Títulos --}}
    <h1>Calificar Entrega</h1>
    <h4>Tarea: {{ $tarea->titulo }}</h4>
    <h5>Curso: {{ $curso->titulo }}</h5>
    <hr>

    {{-- Información del Estudiante y Entrega --}}
    <div class="card mb-4">
        <div class="card-header">
            Información de la Entrega
        </div>
        <div class="card-body">
            <p><strong>Estudiante:</strong> {{ optional($entrega->estudiante)->nombre }} {{ optional($entrega->estudiante)->apellidos }} ({{ optional($entrega->estudiante)->email }})</p>
            <p><strong>Fecha de Entrega:</strong> {{ $entrega->fecha_entrega->format('d/m/Y H:i') }}
                @if($entrega->estado_entrega == 'entregado_tarde') <span class="badge bg-warning text-dark">Tarde</span> @endif
            </p>

            {{-- Mostrar Contenido Entregado --}}
            <h6 class="mt-3">Contenido Entregado:</h6>
            @if($entrega->ruta_archivo)
                <p><a href="{{ Storage::url($entrega->ruta_archivo) }}" target="_blank" class="btn btn-outline-primary">Ver/Descargar Archivo Entregado</a></p>
            @elseif($entrega->texto_entrega)
                <div class="p-3 bg-light border rounded">
                    <pre style="white-space: pre-wrap; word-wrap: break-word;">{{ $entrega->texto_entrega }}</pre>
                </div>
            @elseif($entrega->url_entrega)
                <p><a href="{{ $entrega->url_entrega }}" target="_blank" rel="noopener noreferrer">Ver Enlace Entregado</a></p>
            @else
                <p class="text-muted">No hay contenido adjunto para esta entrega (posiblemente tipo 'ninguno' o error).</p>
            @endif
        </div>
    </div>

    {{-- Formulario de Calificación --}}
    <div class="card">
        <div class="card-header">
            Calificación y Retroalimentación
        </div>
        <div class="card-body">
            {{-- Formulario apunta a la ruta guardarCalificacion --}}
            <form action="{{ route('docente.cursos.tareas.entregas.calificar.store', [$curso->id, $tarea->id, $entrega->id]) }}" method="POST">
                @csrf
                @method('PATCH') {{-- O PUT, según definiste en web.php --}}

                {{-- Campo Calificación --}}
                <div class="mb-3">
                    <label for="calificacion" class="form-label">Calificación <span class="text-danger">*</span> (sobre {{ $tarea->puntos_maximos ?? 'N/A' }})</label>
                    <input type="number" step="0.01" min="0" {{ $tarea->puntos_maximos ? 'max='.$tarea->puntos_maximos : '' }}
                           class="form-control @error('calificacion') is-invalid @enderror"
                           id="calificacion" name="calificacion"
                           value="{{ old('calificacion', $entrega->calificacion) }}" {{-- Muestra calificación existente --}}
                           required>
                    @error('calificacion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Campo Retroalimentación --}}
                <div class="mb-3">
                    <label for="retroalimentacion" class="form-label">Retroalimentación (Comentarios para el estudiante)</label>
                    <textarea class="form-control @error('retroalimentacion') is-invalid @enderror"
                              id="retroalimentacion" name="retroalimentacion"
                              rows="5">{{ old('retroalimentacion', $entrega->retroalimentacion) }}</textarea> {{-- Muestra retroalimentación existente --}}
                    @error('retroalimentacion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Botones --}}
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Guardar Calificación</button>
                    <a href="{{ route('docente.cursos.tareas.entregas.index', [$curso->id, $tarea->id]) }}" class="btn btn-secondary">Cancelar</a>
                </div>

            </form>
        </div>
    </div>

</div>
@endsection
