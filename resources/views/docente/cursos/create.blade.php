@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <div class="card shadow">
                <div class="card-header border-0 bg-primary text-white p-4">
                    <div class="d-flex align-items-center">
                        <div class="card-header-icon me-3">
                            <i class="fas fa-plus-circle fa-2x"></i>
                        </div>
                        <div>
                            <h4 class="mb-1">Crear Nuevo Curso</h4>
                            <p class="mb-0 opacity-75">Completa la información para crear un nuevo curso</p>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('docente.cursos.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- Campo Título --}}
                        <div class="form-group mb-4">
                            <label for="titulo" class="form-label fw-bold">
                                <i class="fas fa-heading me-2 text-primary"></i>Título del Curso
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control form-control-lg @error('titulo') is-invalid @enderror" 
                                   id="titulo" 
                                   name="titulo" 
                                   value="{{ old('titulo') }}" 
                                   required 
                                   placeholder="Ej: Cálculo Diferencial Avanzado">
                            @error('titulo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Campo Código del Curso --}}
                        <div class="form-group mb-4">
                            <label for="codigo_curso" class="form-label fw-bold">
                                <i class="fas fa-hashtag me-2 text-primary"></i>Código del Curso
                            </label>
                            <input type="text" 
                                   class="form-control @error('codigo_curso') is-invalid @enderror" 
                                   id="codigo_curso" 
                                   name="codigo_curso" 
                                   value="{{ old('codigo_curso') }}" 
                                   placeholder="Ej: MAT-301">
                            @error('codigo_curso')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">El código ayuda a identificar el curso de manera única</div>
                        </div>

                        {{-- Campo Descripción --}}
                        <div class="form-group mb-4">
                            <label for="descripcion" class="form-label fw-bold">
                                <i class="fas fa-align-left me-2 text-primary"></i>Descripción
                            </label>
                            <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                      id="descripcion" 
                                      name="descripcion" 
                                      rows="4" 
                                      placeholder="Introduce una breve descripción del contenido, objetivos y temas principales del curso...">{{ old('descripcion') }}</textarea>
                            @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            {{-- Campo Carrera --}}
                            <div class="col-md-6 mb-4">
                                <label for="carrera_id" class="form-label fw-bold">
                                    <i class="fas fa-graduation-cap me-2 text-primary"></i>Carrera
                                </label>
                                <select class="form-select @error('carrera_id') is-invalid @enderror" 
                                        id="carrera_id" 
                                        name="carrera_id">
                                    <option value="">-- Selecciona una Carrera --</option>
                                    @if(isset($carreras))
                                        @foreach($carreras as $id => $nombre)
                                            <option value="{{ $id }}" {{ old('carrera_id') == $id ? 'selected' : '' }}>
                                                {{ $nombre }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('carrera_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Campo Estado --}}
                            <div class="col-md-6 mb-4">
                                <label for="estado" class="form-label fw-bold">
                                    <i class="fas fa-toggle-on me-2 text-primary"></i>Estado
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('estado') is-invalid @enderror" 
                                        id="estado" 
                                        name="estado" 
                                        required>
                                    <option value="borrador" {{ old('estado', 'borrador') == 'borrador' ? 'selected' : '' }}>
                                        <i class="fas fa-edit"></i> Borrador
                                    </option>
                                    <option value="publicado" {{ old('estado') == 'publicado' ? 'selected' : '' }}>
                                        <i class="fas fa-globe"></i> Publicado
                                    </option>
                                    <option value="archivado" {{ old('estado') == 'archivado' ? 'selected' : '' }}>
                                        <i class="fas fa-archive"></i> Archivado
                                    </option>
                                </select>
                                @error('estado')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Fechas --}}
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="fecha_inicio" class="form-label fw-bold">
                                    <i class="fas fa-calendar-alt me-2 text-primary"></i>Fecha de Inicio
                                </label>
                                <input type="date" 
                                       class="form-control @error('fecha_inicio') is-invalid @enderror" 
                                       id="fecha_inicio" 
                                       name="fecha_inicio" 
                                       value="{{ old('fecha_inicio') }}">
                                @error('fecha_inicio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="fecha_fin" class="form-label fw-bold">
                                    <i class="fas fa-calendar-check me-2 text-primary"></i>Fecha de Fin
                                </label>
                                <input type="date" 
                                       class="form-control @error('fecha_fin') is-invalid @enderror" 
                                       id="fecha_fin" 
                                       name="fecha_fin" 
                                       value="{{ old('fecha_fin') }}">
                                @error('fecha_fin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Imagen del Curso --}}
                        <div class="form-group mb-4">
                            <label for="ruta_imagen_curso" class="form-label fw-bold">
                                <i class="fas fa-image me-2 text-primary"></i>Imagen del Curso
                            </label>
                            <div class="input-group">
                                <input class="form-control @error('ruta_imagen_curso') is-invalid @enderror" 
                                       type="file" 
                                       id="ruta_imagen_curso" 
                                       name="ruta_imagen_curso" 
                                       accept="image/jpeg,image/png,image/gif,image/webp">
                            </div>
                            <div class="form-text">
                                Sube una imagen representativa para el curso (Formatos: JPG, PNG, GIF, WEBP)
                            </div>
                            @error('ruta_imagen_curso')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Botones de Acción --}}
                        <div class="d-flex justify-content-end gap-2 mt-5">
                            <a href="{{ route('docente.cursos.index') }}" class="btn btn-light px-4">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>Crear Curso
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Estilos del formulario */
    .card {
        border: none;
        border-radius: 1rem;
        overflow: hidden;
    }

    .card-header {
        position: relative;
    }

    .card-header-icon {
        background: rgba(255, 255, 255, 0.1);
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Campos del formulario */
    .form-control, .form-select {
        border: 2px solid #e9ecef;
        padding: 0.75rem 1rem;
        border-radius: 0.5rem;
        font-size: 0.95rem;
        transition: all 0.2s;
    }

    .form-control:focus, .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1);
    }

    .form-control-lg {
        font-size: 1.1rem;
    }

    .form-label {
        margin-bottom: 0.5rem;
        font-size: 0.925rem;
    }

    /* Botones */
    .btn {
        padding: 0.75rem 1.5rem;
        font-weight: 500;
        border-radius: 0.5rem;
        transition: all 0.2s;
    }

    .btn:hover {
        transform: translateY(-1px);
    }

    .btn-light {
        background: #f8f9fa;
        border-color: #e9ecef;
    }

    .btn-light:hover {
        background: #e9ecef;
    }

    /* File input personalizado */
    .input-group {
        border: 2px dashed #e9ecef;
        border-radius: 0.5rem;
        transition: all 0.2s;
    }

    .input-group:hover {
        border-color: #0d6efd;
    }

    .input-group .form-control {
        border: none;
        background: transparent;
    }

    /* Textos de ayuda */
    .form-text {
        color: #6c757d;
        font-size: 0.85rem;
        margin-top: 0.5rem;
    }

    /* Feedback de validación */
    .invalid-feedback {
        font-size: 0.85rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .container {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .btn {
            width: 100%;
            margin-bottom: 0.5rem;
        }

        .d-flex.justify-content-end {
            flex-direction: column;
        }
    }
</style>
@endpush
