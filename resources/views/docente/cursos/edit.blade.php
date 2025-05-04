@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar Curso: {{ $curso->titulo }}</h1>

    {{-- Formulario para editar el curso --}}
    {{-- Envía los datos por PUT/PATCH a la ruta nombrada 'docente.cursos.update' --}}
    {{-- Pasamos el ID del curso a la ruta --}}
    <form action="{{ route('docente.cursos.update', $curso->id) }}" method="POST">
        @csrf {{-- Token CSRF --}}
        @method('PUT') {{-- Directiva para indicar que es una petición PUT/PATCH --}}

        {{-- Campo Título --}}
        <div class="mb-3">
            <label for="titulo" class="form-label">Título del Curso <span class="text-danger">*</span></label>
            {{-- Usamos old() con fallback al valor actual del curso --}}
            <input type="text" class="form-control @error('titulo') is-invalid @enderror" id="titulo" name="titulo" value="{{ old('titulo', $curso->titulo) }}" required>
            @error('titulo')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Campo Código del Curso (Opcional) --}}
        <div class="mb-3">
            <label for="codigo_curso" class="form-label">Código del Curso (Ej: MAT-101)</label>
            <input type="text" class="form-control @error('codigo_curso') is-invalid @enderror" id="codigo_curso" name="codigo_curso" value="{{ old('codigo_curso', $curso->codigo_curso) }}">
            @error('codigo_curso')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Campo Descripción (Opcional) --}}
        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" name="descripcion" rows="3">{{ old('descripcion', $curso->descripcion) }}</textarea>
            @error('descripcion')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Campo Categoría (Opcional) --}}
        <div class="mb-3">
            <label for="categoria_id" class="form-label">Categoría</label>
            <select class="form-select @error('categoria_id') is-invalid @enderror" id="categoria_id" name="categoria_id">
                <option value="">-- Sin categoría --</option>
                {{-- Usamos old() con fallback a la categoría actual del curso --}}
                @foreach($categorias as $id => $nombre)
                    <option value="{{ $id }}" {{ old('categoria_id', $curso->categoria_id) == $id ? 'selected' : '' }}>{{ $nombre }}</option>
                @endforeach
            </select>
            @error('categoria_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Campo Estado --}}
         <div class="mb-3">
            <label for="estado" class="form-label">Estado <span class="text-danger">*</span></label>
            <select class="form-select @error('estado') is-invalid @enderror" id="estado" name="estado" required>
                 {{-- Usamos old() con fallback al estado actual del curso --}}
                <option value="borrador" {{ old('estado', $curso->estado) == 'borrador' ? 'selected' : '' }}>Borrador</option>
                <option value="publicado" {{ old('estado', $curso->estado) == 'publicado' ? 'selected' : '' }}>Publicado</option>
                <option value="archivado" {{ old('estado', $curso->estado) == 'archivado' ? 'selected' : '' }}>Archivado</option>
            </select>
             @error('estado')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Campo Fecha de Inicio (Opcional) --}}
         <div class="mb-3">
            <label for="fecha_inicio" class="form-label">Fecha de Inicio</label>
            {{-- Formateamos la fecha para el input type="date" si existe --}}
            <input type="date" class="form-control @error('fecha_inicio') is-invalid @enderror" id="fecha_inicio" name="fecha_inicio" value="{{ old('fecha_inicio', $curso->fecha_inicio ? $curso->fecha_inicio->format('Y-m-d') : '') }}">
             @error('fecha_inicio')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Campo Fecha de Fin (Opcional) --}}
        <div class="mb-3">
            <label for="fecha_fin" class="form-label">Fecha de Fin</label>
            <input type="date" class="form-control @error('fecha_fin') is-invalid @enderror" id="fecha_fin" name="fecha_fin" value="{{ old('fecha_fin', $curso->fecha_fin ? $curso->fecha_fin->format('Y-m-d') : '') }}">
             @error('fecha_fin')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Campo para Imagen (si lo implementas) --}}
        {{-- ... --}}

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Actualizar Curso</button>
            <a href="{{ route('docente.cursos.show', $curso->id) }}" class="btn btn-secondary">Cancelar</a> {{-- Enlace para volver a los detalles --}}
        </div>

    </form> {{-- Fin del formulario --}}

</div> {{-- Fin container --}}
@endsection