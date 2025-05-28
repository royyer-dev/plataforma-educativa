@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <div class="card shadow">
                <div class="card-header bg-primary text-white p-4">
                    <div class="d-flex align-items-center">
                        <div class="header-icon me-3">
                            <i class="fas fa-edit fa-2x"></i>
                        </div>
                        <div>
                            <h4 class="mb-1">Editar Módulo</h4>
                            <p class="mb-0 opacity-75">{{ $curso->titulo }}</p>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('docente.cursos.modulos.update', [$curso->id, $modulo->id]) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-4">
                            <label for="titulo" class="form-label fw-bold">
                                <i class="fas fa-heading me-2 text-primary"></i>Título del Módulo
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control form-control-lg @error('titulo') is-invalid @enderror" 
                                   id="titulo" 
                                   name="titulo" 
                                   value="{{ old('titulo', $modulo->titulo) }}" 
                                   required>
                            @error('titulo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label for="descripcion" class="form-label fw-bold">
                                <i class="fas fa-align-left me-2 text-primary"></i>Descripción
                            </label>
                            <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                      id="descripcion" 
                                      name="descripcion" 
                                      rows="4">{{ old('descripcion', $modulo->descripcion) }}</textarea>
                            @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label for="orden" class="form-label fw-bold">
                                <i class="fas fa-sort-numeric-down me-2 text-primary"></i>Orden
                            </label>
                            <input type="number" 
                                   class="form-control @error('orden') is-invalid @enderror" 
                                   id="orden" 
                                   name="orden" 
                                   value="{{ old('orden', $modulo->orden ?? 0) }}" 
                                   min="0">
                            @error('orden')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Los módulos se mostrarán ordenados de menor a mayor número.
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-5">
                            <a href="{{ route('docente.cursos.show', $curso->id) }}" class="btn btn-light px-4">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>Actualizar Módulo
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

    .header-icon {
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

    /* Textos informativos */
    .form-text {
        color: #6c757d;
        font-size: 0.85rem;
        margin-top: 0.5rem;
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