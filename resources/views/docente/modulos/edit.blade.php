@extends('layouts.app')

@section('content')
<div class="container">
    {{-- Mostramos a qué curso pertenece y qué módulo editamos --}}
    <h2>Editar Módulo en Curso: {{ $curso->titulo }}</h2>
    <h4>Módulo: {{ $modulo->titulo }}</h4>

    {{-- El formulario envía a la ruta update, pasando IDs del curso y módulo --}}
    {{-- Usamos $curso y $modulo que fueron pasados desde el controlador --}}
    <form action="{{ route('docente.cursos.modulos.update', [$curso->id, $modulo->id]) }}" method="POST">
        @csrf                           {{-- Token CSRF --}}
        @method('PUT')                  {{-- Método HTTP para actualización --}}

        {{-- Campo Título del Módulo --}}
        <div class="mb-3">
            <label for="titulo" class="form-label">Título del Módulo <span class="text-danger">*</span></label>
            {{-- Usamos old() con fallback al valor actual del módulo --}}
            <input type="text" class="form-control @error('titulo') is-invalid @enderror" id="titulo" name="titulo" value="{{ old('titulo', $modulo->titulo) }}" required>
            @error('titulo')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Campo Descripción del Módulo (Opcional) --}}
        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" name="descripcion" rows="3">{{ old('descripcion', $modulo->descripcion) }}</textarea>
            @error('descripcion')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Campo Orden (Opcional) --}}
        <div class="mb-3">
            <label for="orden" class="form-label">Orden (Numérico, opcional)</label>
            <input type="number" class="form-control @error('orden') is-invalid @enderror" id="orden" name="orden" value="{{ old('orden', $modulo->orden ?? 0) }}" min="0">
            @error('orden')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
             <div class="form-text">Determina el orden en que aparecerán los módulos (menor número primero).</div>
        </div>

        {{-- Botones --}}
        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Actualizar Módulo</button>
            {{-- Enlace para cancelar y volver a la vista del curso --}}
            <a href="{{ route('docente.cursos.show', $curso->id) }}" class="btn btn-secondary">Cancelar</a>
        </div>

    </form>

</div>
@endsection