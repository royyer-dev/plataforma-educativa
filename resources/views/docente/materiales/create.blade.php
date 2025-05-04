@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Añadir Material al Curso: {{ $curso->titulo }}</h2>

    {{-- Necesitamos 'enctype' para subida de archivos --}}
    <form action="{{ route('docente.cursos.materiales.store', $curso->id) }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Campo Título del Material --}}
        <div class="mb-3">
            <label for="titulo" class="form-label">Título del Material <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('titulo') is-invalid @enderror" id="titulo" name="titulo" value="{{ old('titulo') }}" required>
            {{-- Muestra error para 'titulo' --}}
            @error('titulo')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Campo Descripción (Opcional) --}}
        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" name="descripcion" rows="2">{{ old('descripcion') }}</textarea>
            {{-- Muestra error para 'descripcion' --}}
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
                    <option value="{{ $id }}" {{ old('modulo_id') == $id ? 'selected' : '' }}>{{ $titulo }}</option>
                @endforeach
            </select>
            {{-- Muestra error para 'modulo_id' --}}
            @error('modulo_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Selector Tipo de Material --}}
        <div class="mb-3">
            <label for="tipo_material" class="form-label">Tipo de Material <span class="text-danger">*</span></label>
            <select class="form-select @error('tipo_material') is-invalid @enderror" id="tipo_material" name="tipo_material" required onchange="toggleMaterialFields()">
                <option value="" disabled {{ old('tipo_material') ? '' : 'selected' }}>-- Selecciona tipo --</option>
                <option value="archivo" {{ old('tipo_material') == 'archivo' ? 'selected' : '' }}>Archivo (PDF, DOC, etc.)</option>
                <option value="enlace" {{ old('tipo_material') == 'enlace' ? 'selected' : '' }}>Enlace Web / URL</option>
                <option value="texto" {{ old('tipo_material') == 'texto' ? 'selected' : '' }}>Texto / Contenido Embebido</option>
                <option value="video" {{ old('tipo_material') == 'video' ? 'selected' : '' }}>Video (Enlace o Embebido)</option>
            </select>
             {{-- Muestra error para 'tipo_material' --}}
             @error('tipo_material')
                <div class="invalid-feedback d-block">{{ $message }}</div> {{-- Usar d-block para que se vea aunque el select no sea inválido visualmente --}}
            @enderror
        </div>

        {{-- Campos Condicionales según el Tipo --}}
        {{-- Campo Archivo --}}
        <div id="campo-archivo" class="mb-3" style="display: {{ old('tipo_material') == 'archivo' ? 'block' : 'none' }};">
            <label for="ruta_archivo" class="form-label">Seleccionar Archivo</label>
            <input class="form-control @error('ruta_archivo') is-invalid @enderror" type="file" id="ruta_archivo" name="ruta_archivo">
            {{-- Muestra error para 'ruta_archivo' --}}
            @error('ruta_archivo')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Campo Enlace/Video URL --}}
        <div id="campo-enlace" class="mb-3" style="display: {{ old('tipo_material') == 'enlace' || old('tipo_material') == 'video' ? 'block' : 'none' }};">
            <label for="enlace_url" class="form-label">URL / Enlace</label>
            <input type="url" class="form-control @error('enlace_url') is-invalid @enderror" id="enlace_url" name="enlace_url" value="{{ old('enlace_url') }}" placeholder="https://...">
             {{-- Muestra error para 'enlace_url' --}}
             @error('enlace_url')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

         {{-- Campo Texto/Contenido --}}
        <div id="campo-texto" class="mb-3" style="display: {{ old('tipo_material') == 'texto' ? 'block' : 'none' }};">
            <label for="contenido_texto" class="form-label">Contenido de Texto / Código Embebido</label>
            <textarea class="form-control @error('contenido_texto') is-invalid @enderror" id="contenido_texto" name="contenido_texto" rows="5">{{ old('contenido_texto') }}</textarea>
             {{-- Muestra error para 'contenido_texto' --}}
             @error('contenido_texto')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
             <div class="form-text">Puedes pegar código HTML para embeber videos aquí si seleccionaste "Texto".</div>
        </div>
        {{-- Fin Campos Condicionales --}}


        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Guardar Material</button>
            <a href="{{ route('docente.cursos.show', $curso->id) }}" class="btn btn-secondary">Cancelar</a>
        </div>

    </form>

</div>

{{-- Script simple para mostrar/ocultar campos según el tipo --}}
<script>
    function toggleMaterialFields() {
        const tipo = document.getElementById('tipo_material').value;
        document.getElementById('campo-archivo').style.display = (tipo === 'archivo') ? 'block' : 'none';
        document.getElementById('campo-enlace').style.display = (tipo === 'enlace' || tipo === 'video') ? 'block' : 'none';
        document.getElementById('campo-texto').style.display = (tipo === 'texto') ? 'block' : 'none';

        // Limpiar valores de campos ocultos (opcional pero recomendado)
        if (tipo !== 'archivo') document.getElementById('ruta_archivo').value = '';
        if (tipo !== 'enlace' && tipo !== 'video') document.getElementById('enlace_url').value = '';
        if (tipo !== 'texto') document.getElementById('contenido_texto').value = '';

        // Nota: La validación principal debe estar en el backend
    }
    // Ejecutar al cargar la página por si hay old input
    document.addEventListener('DOMContentLoaded', toggleMaterialFields);
</script>

@endsection
