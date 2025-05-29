@extends('layouts.app')

@push('styles')
<style>
    /* Estilos para el formulario */
    .form-control, .form-select {
        border: 1px solid #e2e8f0;
        padding: 0.75rem 1rem;
        border-radius: 0.5rem;
        transition: all 0.2s ease;
    }
    .form-control:focus, .form-select:focus {
        border-color: #ffc107;
        box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
    }

    /* Estilos para la tarjeta */
    .card {
        border: none;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        border-radius: 1rem;
        overflow: hidden;
    }
    .card-header {
        background-color: #ffc107;
        padding: 1.25rem;
        border-bottom: none;
    }
    .card-header h4 {
        margin: 0;
        color: #2c3e50;
        font-weight: 600;
    }

    /* Estilos para las etiquetas */
    .form-label {
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }
    .form-label.fw-bold {
        font-weight: 600 !important;
    }

    /* Estilos para los campos requeridos */
    .text-danger {
        font-weight: bold;
        font-size: 1.2em;
    }

    /* Estilos para la previsualización de imagen */
    .image-preview {
        border-radius: 0.5rem;
        border: 2px solid #e2e8f0;
        padding: 0.5rem;
        background: #f8f9fa;
        transition: all 0.2s ease;
    }
    .image-preview:hover {
        border-color: #ffc107;
    }
    .image-preview img {
        border-radius: 0.25rem;
        max-width: 100%;
        height: auto;
    }

    /* Estilos para los botones */
    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    .btn:hover {
        transform: translateY(-1px);
    }
    .btn-warning {
        background-color: #ffc107;
        border: none;
        color: #2c3e50;
    }
    .btn-warning:hover {
        background-color: #ffca2c;
        box-shadow: 0 4px 6px -1px rgba(255, 193, 7, 0.3);
    }
    .btn-outline-secondary {
        border: 2px solid #6c757d;
    }
    .btn-outline-secondary:hover {
        background-color: #6c757d;
        color: white;
    }

    /* Animaciones para los iconos */
    .fas {
        transition: transform 0.2s ease;
    }
    .btn:hover .fas {
        transform: scale(1.1);
    }

    /* Separador personalizado */
    hr {
        border: none;
        height: 2px;
        background: linear-gradient(to right, #ffc107, transparent);
        opacity: 0.2;
    }
</style>
@endpush

@section('content')
<div class="container py-4"> {{-- Añadido padding general al container --}}
    <div class="row justify-content-center"> {{-- Centrar el contenido del formulario --}}
        <div class="col-md-8 col-lg-7"> {{-- Definir un ancho máximo para el formulario --}}
            <div class="card shadow-sm"> {{-- Envolver el formulario en una tarjeta con sombra --}}
                <div class="card-header bg-warning text-dark"> {{-- Encabezado de la tarjeta (color warning para editar) --}}
                    <h4 class="mb-0"><i class="fas fa-edit me-2"></i>Editar Curso: {{ $curso->titulo }}</h4>
                </div>
                <div class="card-body">
                    {{-- Formulario para editar el curso --}}
                    {{-- vvv AÑADIDO enctype para subida de archivos vvv --}}
                    <form action="{{ route('docente.cursos.update', $curso->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf {{-- Token CSRF --}}
                        @method('PUT') {{-- Directiva para indicar que es una petición PUT/PATCH --}}

                        {{-- Campo Título --}}
                        <div class="mb-3">
                            <label for="titulo" class="form-label fw-bold">Título del Curso <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('titulo') is-invalid @enderror" id="titulo" name="titulo" value="{{ old('titulo', $curso->titulo) }}" required placeholder="Ej: Cálculo Diferencial Avanzado">
                            @error('titulo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Campo Código del Curso (Opcional) --}}
                        <div class="mb-3">
                            <label for="codigo_curso" class="form-label fw-bold">Código del Curso</label>
                            <input type="text" class="form-control @error('codigo_curso') is-invalid @enderror" id="codigo_curso" name="codigo_curso" value="{{ old('codigo_curso', $curso->codigo_curso) }}" placeholder="Ej: MAT-301 (Opcional)">
                            @error('codigo_curso')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Campo Descripción (Opcional) --}}
                        <div class="mb-3">
                            <label for="descripcion" class="form-label fw-bold">Descripción</label>
                            <textarea class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" name="descripcion" rows="4" placeholder="Introduce una breve descripción del contenido, objetivos y temas principales del curso...">{{ old('descripcion', $curso->descripcion) }}</textarea>
                            @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Campo Carrera --}}
                        <div class="mb-3">
                            <label for="carrera_id" class="form-label fw-bold">Carrera</label>
                            <select class="form-select @error('carrera_id') is-invalid @enderror" id="carrera_id" name="carrera_id">
                                <option value="">-- Selecciona una Carrera (Opcional) --</option>
                                {{-- Iterar sobre $carreras pasadas desde el controlador --}}
                                @if(isset($carreras))
                                    @foreach($carreras as $id => $nombre)
                                        {{-- Usamos old() con fallback a la carrera actual del curso --}}
                                        <option value="{{ $id }}" {{ old('carrera_id', $curso->carrera_id) == $id ? 'selected' : '' }}>{{ $nombre }}</option>
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
                                {{-- Usamos old() con fallback al estado actual del curso --}}
                                <option value="borrador" {{ old('estado', $curso->estado) == 'borrador' ? 'selected' : '' }}>Borrador (No visible para alumnos)</option>
                                <option value="publicado" {{ old('estado', $curso->estado) == 'publicado' ? 'selected' : '' }}>Publicado (Visible para alumnos)</option>
                                <option value="archivado" {{ old('estado', $curso->estado) == 'archivado' ? 'selected' : '' }}>Archivado (Oculto, no activo)</option>
                            </select>
                             @error('estado')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Fila para Fechas --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="fecha_inicio" class="form-label fw-bold">Fecha de Inicio</label>
                                {{-- Formateamos la fecha para el input type="date" si existe --}}
                                <input type="date" class="form-control @error('fecha_inicio') is-invalid @enderror" id="fecha_inicio" name="fecha_inicio" value="{{ old('fecha_inicio', $curso->fecha_inicio ? $curso->fecha_inicio->format('Y-m-d') : '') }}">
                                 @error('fecha_inicio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="fecha_fin" class="form-label fw-bold">Fecha de Fin</label>
                                <input type="date" class="form-control @error('fecha_fin') is-invalid @enderror" id="fecha_fin" name="fecha_fin" value="{{ old('fecha_fin', $curso->fecha_fin ? $curso->fecha_fin->format('Y-m-d') : '') }}">
                                 @error('fecha_fin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Campo para Imagen --}}
                        <div class="mb-3">
                            <label for="ruta_imagen_curso" class="form-label fw-bold">
                                <i class="fas fa-image me-2 text-primary"></i>Imagen del Curso
                            </label>
                            <input class="form-control @error('ruta_imagen_curso') is-invalid @enderror" 
                                   type="file" 
                                   id="ruta_imagen_curso" 
                                   name="ruta_imagen_curso" 
                                   accept="image/jpeg,image/png,image/gif,image/webp">
                            <small class="form-text text-muted">Formatos permitidos: JPG, PNG, GIF, WEBP. Tamaño máximo: 2MB</small>
                            
                            @if($curso->ruta_imagen_curso)
                                <div class="mt-2">
                                    <small>Imagen actual:</small><br>
                                    <img src="{{ asset('storage/' . $curso->ruta_imagen_curso) }}" 
                                         alt="Imagen actual del curso {{ $curso->titulo }}" 
                                         class="mt-2 rounded"
                                         style="max-height: 100px;"
                                         onerror="this.onerror=null; this.src='{{ asset('images/default_course.png') }}';">
                                </div>
                            @endif
                            
                            @error('ruta_imagen_curso')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4"> {{-- Separador visual --}}

                        {{-- Botones de Acción --}}
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('docente.cursos.show', $curso->id) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-warning"> {{-- Botón de color warning para actualizar --}}
                                <i class="fas fa-save me-1"></i> Actualizar Curso
                            </button>
                        </div>

                    </form> {{-- Fin del formulario --}}
                </div> {{-- Fin card-body --}}
            </div> {{-- Fin card --}}
        </div> {{-- Fin col --}}
    </div> {{-- Fin row --}}
</div> {{-- Fin container --}}
@endsection
