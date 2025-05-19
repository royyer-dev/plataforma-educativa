@extends('layouts.app') {{-- Usa tu layout principal --}}

@section('content')
<div class="container py-4"> {{-- Añadido padding general al container --}}
    <div class="row justify-content-center"> {{-- Centrar el contenido del formulario --}}
        <div class="col-md-8 col-lg-7"> {{-- Definir un ancho máximo para el formulario --}}
            <div class="card shadow-sm"> {{-- Envolver el formulario en una tarjeta con sombra --}}
                <div class="card-header bg-primary text-white"> {{-- Encabezado de la tarjeta --}}
                    <h4 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Crear Nuevo Curso</h4>
                </div>
                <div class="card-body">
                    {{-- Formulario para crear el curso --}}
                    {{-- vvv AÑADIDO: enctype="multipart/form-data" para subida de archivos vvv --}}
                    <form action="{{ route('docente.cursos.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf {{-- Token CSRF obligatorio --}}

                        {{-- Campo Título --}}
                        <div class="mb-3">
                            <label for="titulo" class="form-label fw-bold">Título del Curso <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('titulo') is-invalid @enderror" id="titulo" name="titulo" value="{{ old('titulo') }}" required placeholder="Ej: Cálculo Diferencial Avanzado">
                            @error('titulo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Campo Código del Curso (Opcional) --}}
                        <div class="mb-3">
                            <label for="codigo_curso" class="form-label fw-bold">Código del Curso</label>
                            <input type="text" class="form-control @error('codigo_curso') is-invalid @enderror" id="codigo_curso" name="codigo_curso" value="{{ old('codigo_curso') }}" placeholder="Ej: MAT-301 (Opcional)">
                            @error('codigo_curso')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Campo Descripción (Opcional) --}}
                        <div class="mb-3">
                            <label for="descripcion" class="form-label fw-bold">Descripción</label>
                            <textarea class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" name="descripcion" rows="4" placeholder="Introduce una breve descripción del contenido, objetivos y temas principales del curso...">{{ old('descripcion') }}</textarea>
                            @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Campo Carrera --}}
                        <div class="mb-3">
                            <label for="carrera_id" class="form-label fw-bold">Carrera</label>
                            <select class="form-select @error('carrera_id') is-invalid @enderror" id="carrera_id" name="carrera_id">
                                <option value="">-- Selecciona una Carrera (Opcional) --</option>
                                @if(isset($carreras))
                                    @foreach($carreras as $id => $nombre)
                                        <option value="{{ $id }}" {{ old('carrera_id') == $id ? 'selected' : '' }}>{{ $nombre }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('carrera_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Campo Estado --}}
                        <div class="mb-3">
                            <label for="estado" class="form-label fw-bold">Estado <span class="text-danger">*</span></label>
                            <select class="form-select @error('estado') is-invalid @enderror" id="estado" name="estado" required>
                                <option value="borrador" {{ old('estado', 'borrador') == 'borrador' ? 'selected' : '' }}>Borrador (No visible para alumnos)</option>
                                <option value="publicado" {{ old('estado') == 'publicado' ? 'selected' : '' }}>Publicado (Visible para alumnos)</option>
                                <option value="archivado" {{ old('estado') == 'archivado' ? 'selected' : '' }}>Archivado (Oculto, no activo)</option>
                            </select>
                             @error('estado')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Fila para Fechas --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="fecha_inicio" class="form-label fw-bold">Fecha de Inicio</label>
                                <input type="date" class="form-control @error('fecha_inicio') is-invalid @enderror" id="fecha_inicio" name="fecha_inicio" value="{{ old('fecha_inicio') }}">
                                 @error('fecha_inicio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="fecha_fin" class="form-label fw-bold">Fecha de Fin</label>
                                <input type="date" class="form-control @error('fecha_fin') is-invalid @enderror" id="fecha_fin" name="fecha_fin" value="{{ old('fecha_fin') }}">
                                 @error('fecha_fin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Campo para ruta_imagen_curso (Opcional) --}}
                        <div class="mb-3">
                            <label for="ruta_imagen_curso" class="form-label fw-bold">Imagen del Curso (Opcional)</label>
                            <input class="form-control @error('ruta_imagen_curso') is-invalid @enderror" type="file" id="ruta_imagen_curso" name="ruta_imagen_curso" accept="image/jpeg,image/png,image/gif,image/webp">
                             <small class="form-text text-muted">Sube una imagen representativa para el curso (ej: JPG, PNG, GIF, WEBP).</small>
                            @error('ruta_imagen_curso')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4"> {{-- Separador visual --}}

                        {{-- Botones de Acción --}}
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end"> {{-- Alinea botones a la derecha en md y más grandes --}}
                            <a href="{{ route('docente.cursos.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Guardar Curso
                            </button>
                        </div>

                    </form> {{-- Fin del formulario --}}
                </div> {{-- Fin card-body --}}
            </div> {{-- Fin card --}}
        </div> {{-- Fin col --}}
    </div> {{-- Fin row --}}
</div> {{-- Fin container --}}
@endsection
