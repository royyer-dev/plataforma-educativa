@extends('layouts.app')

@section('content')
<div class="container py-4">
    {{-- Navegación y Título --}}
    <nav aria-label="breadcrumb" class="mb-2">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('docente.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('docente.cursos.show', $curso->id) }}">{{ Str::limit($curso->titulo, 30) }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Nueva Tarea</li>
        </ol>
    </nav>

    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex align-items-center">
            <i class="fas fa-tasks me-2 text-primary"></i>
            <h2 class="h5 mb-0">Crear Nueva Tarea</h2>
        </div>
        <div class="card-body">
            <form action="{{ route('docente.cursos.tareas.store', $curso->id) }}" method="POST">
                @csrf
                
                <div class="row">
                    {{-- Columna Izquierda: Información Principal --}}
                    <div class="col-md-6 border-end">
                        <h6 class="fw-bold mb-3 text-primary">Información Principal</h6>
                        
                        {{-- Título --}}
                        <div class="mb-3">
                            <label for="titulo" class="form-label">
                                <i class="fas fa-heading me-1 text-secondary"></i>
                                Título <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('titulo') is-invalid @enderror" 
                                   id="titulo" name="titulo" value="{{ old('titulo') }}" required
                                   placeholder="Ej: Proyecto Final - Análisis de Datos">
                            @error('titulo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Descripción --}}
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">
                                <i class="fas fa-align-left me-1 text-secondary"></i>
                                Descripción / Instrucciones
                            </label>
                            <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                      id="descripcion" name="descripcion" rows="4"
                                      placeholder="Detalla las instrucciones y requerimientos de la tarea...">{{ old('descripcion') }}</textarea>
                            @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Módulo --}}
                        <div class="mb-3">
                            <label for="modulo_id" class="form-label">
                                <i class="fas fa-folder me-1 text-secondary"></i>
                                Módulo
                            </label>
                            <select class="form-select @error('modulo_id') is-invalid @enderror" id="modulo_id" name="modulo_id">
                                <option value="">General del Curso / Sin Módulo</option>
                                @foreach($modulos as $id => $titulo)
                                    <option value="{{ $id }}" {{ old('modulo_id') == $id ? 'selected' : '' }}>{{ $titulo }}</option>
                                @endforeach
                            </select>
                            @error('modulo_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Columna Derecha: Configuración de Entrega --}}
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3 text-primary">Configuración de Entrega</h6>

                        {{-- Tipo de Entrega --}}
                        <div class="mb-3">
                            <label for="tipo_entrega" class="form-label">
                                <i class="fas fa-file-upload me-1 text-secondary"></i>
                                Tipo de Entrega <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('tipo_entrega') is-invalid @enderror" 
                                    id="tipo_entrega" name="tipo_entrega" required>
                                <option value="" disabled {{ old('tipo_entrega') ? '' : 'selected' }}>Selecciona el tipo de entrega</option>
                                @foreach($tipos_entrega as $valor => $texto)
                                    <option value="{{ $valor }}" {{ old('tipo_entrega') == $valor ? 'selected' : '' }}>{{ $texto }}</option>
                                @endforeach
                            </select>
                            @error('tipo_entrega')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Puntos Máximos --}}
                        <div class="mb-3">
                            <label for="puntos_maximos" class="form-label">
                                <i class="fas fa-star me-1 text-secondary"></i>
                                Puntos Máximos
                            </label>
                            <input type="number" step="0.01" min="0" 
                                   class="form-control @error('puntos_maximos') is-invalid @enderror" 
                                   id="puntos_maximos" name="puntos_maximos" 
                                   value="{{ old('puntos_maximos') }}"
                                   placeholder="Ej: 100">
                            @error('puntos_maximos')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Fecha Límite --}}
                        <div class="mb-3">
                            <label for="fecha_limite" class="form-label">
                                <i class="fas fa-calendar-alt me-1 text-secondary"></i>
                                Fecha Límite de Entrega
                            </label>
                            <input type="datetime-local" 
                                   class="form-control @error('fecha_limite') is-invalid @enderror" 
                                   id="fecha_limite" name="fecha_limite" 
                                   value="{{ old('fecha_limite') }}">
                            @error('fecha_limite')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Entregas Tardías --}}
                        <div class="card bg-light border mt-4">
                            <div class="card-body">
                                <div class="form-check mb-3">
                                    <input type="checkbox" class="form-check-input" 
                                           id="permite_entrega_tardia" name="permite_entrega_tardia" 
                                           value="1" {{ old('permite_entrega_tardia') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="permite_entrega_tardia">
                                        <i class="fas fa-clock me-1"></i>
                                        Permitir entregas tardías
                                    </label>
                                </div>

                                <div class="mb-0" id="fecha_limite_tardia_container">
                                    <label for="fecha_limite_tardia" class="form-label">
                                        <i class="fas fa-calendar-times me-1 text-secondary"></i>
                                        Fecha Límite para Entregas Tardías
                                    </label>
                                    <input type="datetime-local" 
                                           class="form-control @error('fecha_limite_tardia') is-invalid @enderror" 
                                           id="fecha_limite_tardia" name="fecha_limite_tardia" 
                                           value="{{ old('fecha_limite_tardia') }}">
                                    @error('fecha_limite_tardia')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Botones --}}
                <div class="border-top mt-4 pt-4 text-end">
                    <a href="{{ route('docente.cursos.show', $curso->id) }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Guardar Tarea
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const permiteTardiaCheckbox = document.getElementById('permite_entrega_tardia');
    const fechaTardiaContainer = document.getElementById('fecha_limite_tardia_container');

    function toggleFechaTardia() {
        fechaTardiaContainer.style.display = permiteTardiaCheckbox.checked ? 'block' : 'none';
    }

    permiteTardiaCheckbox.addEventListener('change', toggleFechaTardia);
    toggleFechaTardia(); // Estado inicial
});
</script>
@endpush

@endsection
