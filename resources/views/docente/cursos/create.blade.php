@extends('layouts.app') {{-- Usa tu layout principal --}}

@section('content')
<div class="container">
    <h1>Crear Nuevo Curso</h1>

    {{-- Formulario para crear el curso --}}
    {{-- Envía los datos por POST a la ruta nombrada 'docente.cursos.store' --}}
    <form action="{{ route('docente.cursos.store') }}" method="POST">
        @csrf {{-- Token CSRF obligatorio para seguridad en formularios POST --}}

        {{-- Campo Título --}}
        <div class="mb-3">
            <label for="titulo" class="form-label">Título del Curso <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('titulo') is-invalid @enderror" id="titulo" name="titulo" value="{{ old('titulo') }}" required>
            {{-- Muestra error de validación para 'titulo' si existe --}}
            @error('titulo')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Campo Código del Curso (Opcional) --}}
        <div class="mb-3">
            <label for="codigo_curso" class="form-label">Código del Curso (Ej: MAT-101)</label>
            <input type="text" class="form-control @error('codigo_curso') is-invalid @enderror" id="codigo_curso" name="codigo_curso" value="{{ old('codigo_curso') }}">
            @error('codigo_curso')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Campo Descripción (Opcional) --}}
        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" name="descripcion" rows="3">{{ old('descripcion') }}</textarea>
            @error('descripcion')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Campo Categoría (Opcional) --}}
        <div class="mb-3">
            <label for="categoria_id" class="form-label">Categoría</label>
            {{-- Asume que $categorias fue pasado desde el controlador con pluck('nombre', 'id') --}}
            <select class="form-select @error('categoria_id') is-invalid @enderror" id="categoria_id" name="categoria_id">
                <option value="">-- Sin categoría --</option>
                @foreach($categorias as $id => $nombre)
                    <option value="{{ $id }}" {{ old('categoria_id') == $id ? 'selected' : '' }}>{{ $nombre }}</option>
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
                {{-- Usamos old() para recordar la selección si falla la validación --}}
                <option value="borrador" {{ old('estado', 'borrador') == 'borrador' ? 'selected' : '' }}>Borrador</option>
                <option value="publicado" {{ old('estado') == 'publicado' ? 'selected' : '' }}>Publicado</option>
                <option value="archivado" {{ old('estado') == 'archivado' ? 'selected' : '' }}>Archivado</option>
            </select>
             @error('estado')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Campo Fecha de Inicio (Opcional) --}}
         <div class="mb-3">
            <label for="fecha_inicio" class="form-label">Fecha de Inicio</label>
            <input type="date" class="form-control @error('fecha_inicio') is-invalid @enderror" id="fecha_inicio" name="fecha_inicio" value="{{ old('fecha_inicio') }}">
             @error('fecha_inicio')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Campo Fecha de Fin (Opcional) --}}
        <div class="mb-3">
            <label for="fecha_fin" class="form-label">Fecha de Fin</label>
            <input type="date" class="form-control @error('fecha_fin') is-invalid @enderror" id="fecha_fin" name="fecha_fin" value="{{ old('fecha_fin') }}">
             @error('fecha_fin')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Si manejaras subida de imágenes, iría aquí --}}
        {{-- <div class="mb-3">
            <label for="ruta_imagen_curso" class="form-label">Imagen del Curso</label>
            <input class="form-control @error('ruta_imagen_curso') is-invalid @enderror" type="file" id="ruta_imagen_curso" name="ruta_imagen_curso">
            @error('ruta_imagen_curso')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div> --}}


        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Guardar Curso</button>
            <a href="{{ route('docente.cursos.index') }}" class="btn btn-secondary">Cancelar</a> {{-- Enlace para volver al listado --}}
        </div>

    </form> {{-- Fin del formulario --}}

</div> {{-- Fin container --}}
@endsection