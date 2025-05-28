@extends('layouts.app')

@push('styles')
<style>
    .form-container {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
        padding: 2rem;
    }
    .page-title {
        color: #2c3e50;
        border-bottom: 2px solid #3498db;
        padding-bottom: 10px;
        margin-bottom: 25px;
    }
    .form-label {
        font-weight: 600;
        color: #34495e;
    }
    .field-hint {
        color: #7f8c8d;
        font-size: 0.85rem;
        margin-top: 4px;
    }
    .btn-submit {
        padding: 10px 25px;
        font-weight: 600;
    }
    .required-asterisk {
        color: #e74c3c;
        margin-left: 4px;
    }
    .form-control:focus, .form-select:focus {
        border-color: #3498db;
        box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
    }
    .tipo-material-card {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
        background-color: #f8f9fa;
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="form-container">
        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>¡Por favor corrige los siguientes errores!</strong>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        <h2 class="page-title">
            <i class="fas fa-file-upload me-2 text-primary"></i>
            Añadir Material al Curso: <span class="text-primary">{{ $curso->titulo }}</span>
        </h2>

        {{-- Necesitamos 'enctype' para subida de archivos --}}
        <form action="{{ route('docente.cursos.materiales.store', $curso->id) }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
        @csrf            <div class="row">
                <div class="col-md-8">
                    {{-- Campo Título del Material --}}
                    <div class="mb-4">
                        <label for="titulo" class="form-label">
                            Título del Material
                            <span class="required-asterisk">*</span>
                        </label>
                        <input type="text" 
                               class="form-control form-control-lg @error('titulo') is-invalid @enderror" 
                               id="titulo" 
                               name="titulo" 
                               value="{{ old('titulo') }}" 
                               placeholder="Introduce un título descriptivo"
                               required>
                        <div class="field-hint">El título debe ser claro y descriptivo para los estudiantes</div>
                        @error('titulo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

        {{-- Campo Descripción (Opcional) --}}
        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" name="descripcion" rows="2">{{ old('descripcion') }}</textarea>
            {{-- Muestra error para 'descripcion' --}}
            @error('descripcion')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>        {{-- Selector de Módulo (Opcional) --}}
        <div class="mb-4">
            <label for="modulo_id" class="form-label">
                <i class="fas fa-folder me-1"></i>Asignar a Módulo
            </label>
            <select class="form-select form-select-lg @error('modulo_id') is-invalid @enderror" id="modulo_id" name="modulo_id">
                <option value="">📄 Material General del Curso</option>
                @foreach($modulos as $id => $titulo)
                    <option value="{{ $id }}" {{ old('modulo_id') == $id ? 'selected' : '' }}>
                        📚 {{ $titulo }}
                    </option>
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
        {{-- Campo Archivo --}}        <div id="campo-archivo" class="mb-3" style="display: {{ old('tipo_material') == 'archivo' ? 'block' : 'none' }};">
            <label for="ruta_archivo" class="form-label">Seleccionar Archivo <span class="text-danger">*</span></label>
            <input class="form-control @error('ruta_archivo') is-invalid @enderror" type="file" id="ruta_archivo" name="ruta_archivo">
            <div class="form-text">
                Formatos permitidos: PDF, Word, Excel, PowerPoint, ZIP, JPG, PNG, GIF<br>
                Tamaño máximo: 10 MB
            </div>
            @error('ruta_archivo')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        {{-- Progress bar for file upload --}}
        <div id="upload-progress" class="progress mb-3 d-none">
            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
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
        {{-- Fin Campos Condicionales --}}        <div class="mt-4 d-flex justify-content-between">
            <a href="{{ route('docente.cursos.show', $curso->id) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver al Curso
            </a>
            <button type="submit" class="btn btn-primary btn-submit">
                <i class="fas fa-upload me-2"></i>Subir Material
            </button>
        </div>

        </form>
    </div>
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const fileInput = document.getElementById('ruta_archivo');
        const progressBar = document.querySelector('.progress-bar');
        const progressDiv = document.getElementById('upload-progress');
        const submitBtn = document.querySelector('.btn-submit');
        
        // Toggle fields based on material type
        function toggleMaterialFields() {
            const tipo = document.getElementById('tipo_material').value;
            const campoArchivo = document.getElementById('campo-archivo');
            const campoEnlace = document.getElementById('campo-enlace');
            const campoTexto = document.getElementById('campo-texto');
            
            campoArchivo.style.display = (tipo === 'archivo') ? 'block' : 'none';
            campoEnlace.style.display = (tipo === 'enlace' || tipo === 'video') ? 'block' : 'none';
            campoTexto.style.display = (tipo === 'texto') ? 'block' : 'none';
            
            // Clear validation state when switching types
            if (tipo !== 'archivo') fileInput.value = '';
        }
        
        // File validation
        fileInput?.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                // Check file size (10MB max)
                if (file.size > 10 * 1024 * 1024) {
                    alert('El archivo es demasiado grande. El tamaño máximo permitido es 10MB.');
                    this.value = '';
                    return;
                }
                
                // Check file type
                const allowedTypes = [
                    'application/pdf', 
                    'application/msword', 
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/vnd.ms-powerpoint',
                    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                    'application/zip',
                    'image/jpeg',
                    'image/png',
                    'image/gif'
                ];
                
                if (!allowedTypes.includes(file.type)) {
                    alert('Tipo de archivo no permitido. Por favor, sube un archivo en formato válido.');
                    this.value = '';
                    return;
                }
            }
        });
        
        // Form submission
        form.addEventListener('submit', function(e) {
            const tipo = document.getElementById('tipo_material').value;
            
            // Validate required fields
            if (!this.checkValidity()) {
                e.preventDefault();
                return false;
            }
            
            // File type specific validation
            if (tipo === 'archivo' && !fileInput.files[0]) {
                e.preventDefault();
                alert('Por favor selecciona un archivo para subir.');
                return false;
            }
            
            // Show progress bar for file uploads
            if (tipo === 'archivo' && fileInput.files[0]) {
                progressDiv.classList.remove('d-none');
                submitBtn.disabled = true;
                
                // Simulate upload progress (in a real app, you'd use XHR or Fetch API)
                let progress = 0;
                const interval = setInterval(() => {
                    progress += 5;
                    progressBar.style.width = progress + '%';
                    progressBar.setAttribute('aria-valuenow', progress);
                    
                    if (progress >= 100) {
                        clearInterval(interval);
                        form.submit();
                    }
                }, 100);
            }
        });
    });
</script>

@endsection
