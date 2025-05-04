@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Editar Tarea en Curso: {{ $curso->titulo }}</h2>
    <h4>Tarea: {{ $tarea->titulo }}</h4>

    {{-- Formulario apunta a la ruta update, pasando IDs --}}
    <form action="{{ route('docente.cursos.tareas.update', [$curso->id, $tarea->id]) }}" method="POST">
        @csrf
        @method('PUT') {{-- Método HTTP para actualizar --}}

        {{-- Campo Título de la Tarea --}}
        <div class="mb-3">
            <label for="titulo" class="form-label">Título de la Tarea <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('titulo') is-invalid @enderror" id="titulo" name="titulo" value="{{ old('titulo', $tarea->titulo) }}" required>
            @error('titulo')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Campo Descripción / Instrucciones --}}
        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción / Instrucciones</label>
            <textarea class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" name="descripcion" rows="5">{{ old('descripcion', $tarea->descripcion) }}</textarea>
            @error('descripcion')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

         {{-- Selector de Módulo (Opcional) --}}
        <div class="mb-3">
            <label for="modulo_id" class="form-label">Asignar a Módulo (Opcional)</label>
            <select class="form-select @error('modulo_id') is-invalid @enderror" id="modulo_id" name="modulo_id">
                <option value="">-- General del Curso / Sin Módulo --</option>
                @foreach($modulos as $id => $titulo)
                    {{-- Usamos old() con fallback al módulo actual de la tarea --}}
                    <option value="{{ $id }}" {{ old('modulo_id', $tarea->modulo_id) == $id ? 'selected' : '' }}>{{ $titulo }}</option>
                @endforeach
            </select>
            @error('modulo_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Selector Tipo de Entrega --}}
        <div class="mb-3">
            <label for="tipo_entrega" class="form-label">Tipo de Entrega <span class="text-danger">*</span></label>
            <select class="form-select @error('tipo_entrega') is-invalid @enderror" id="tipo_entrega" name="tipo_entrega" required>
                <option value="" disabled {{ old('tipo_entrega', $tarea->tipo_entrega) ? '' : 'selected' }}>-- Selecciona tipo --</option>
                @foreach($tipos_entrega as $valor => $texto)
                     {{-- Usamos old() con fallback al tipo actual de la tarea --}}
                     <option value="{{ $valor }}" {{ old('tipo_entrega', $tarea->tipo_entrega) == $valor ? 'selected' : '' }}>{{ $texto }}</option>
                @endforeach
            </select>
             @error('tipo_entrega')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        {{-- Campo Puntos Máximos (Opcional) --}}
        <div class="mb-3">
            <label for="puntos_maximos" class="form-label">Puntos Máximos (Ej: 100)</label>
            <input type="number" step="0.01" min="0" class="form-control @error('puntos_maximos') is-invalid @enderror" id="puntos_maximos" name="puntos_maximos" value="{{ old('puntos_maximos', $tarea->puntos_maximos) }}">
            @error('puntos_maximos')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Campo Fecha Límite (Opcional) --}}
         <div class="mb-3">
            <label for="fecha_limite" class="form-label">Fecha Límite de Entrega</label>
             {{-- Formatear fecha/hora para el input si existe --}}
            <input type="datetime-local" class="form-control @error('fecha_limite') is-invalid @enderror" id="fecha_limite" name="fecha_limite" value="{{ old('fecha_limite', $tarea->fecha_limite ? $tarea->fecha_limite->format('Y-m-d\TH:i') : '') }}">
             @error('fecha_limite')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

         {{-- Checkbox Permitir Entrega Tardía --}}
         <div class="mb-3 form-check">
             {{-- Usamos old() con fallback al valor actual de la tarea --}}
            <input type="checkbox" class="form-check-input" id="permite_entrega_tardia" name="permite_entrega_tardia" value="1" {{ old('permite_entrega_tardia', $tarea->permite_entrega_tardia) ? 'checked' : '' }}>
            <label class="form-check-label" for="permite_entrega_tardia">Permitir entregas después de la fecha límite</label>
         </div>

         {{-- Campo Fecha Límite Tardía (Opcional, condicional) --}}
         <div class="mb-3">
            <label for="fecha_limite_tardia" class="form-label">Fecha Límite para Entregas Tardías</label>
             {{-- Formatear fecha/hora para el input si existe --}}
            <input type="datetime-local" class="form-control @error('fecha_limite_tardia') is-invalid @enderror" id="fecha_limite_tardia" name="fecha_limite_tardia" value="{{ old('fecha_limite_tardia', $tarea->fecha_limite_tardia ? $tarea->fecha_limite_tardia->format('Y-m-d\TH:i') : '') }}">
             @error('fecha_limite_tardia')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
             <div class="form-text">Solo aplica si marcaste la casilla anterior.</div>
        </div>


        {{-- Botones --}}
        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Actualizar Tarea</button>
            <a href="{{ route('docente.cursos.show', $curso->id) }}" class="btn btn-secondary">Cancelar</a>
        </div>

    </form>

</div>
@endsection
