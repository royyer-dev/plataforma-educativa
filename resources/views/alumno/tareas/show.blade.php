@extends('layouts.app')

@section('content')
<div class="container py-4">
    {{-- Encabezado Principal --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body py-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h1 class="display-6 mb-0">{{ $tarea->titulo }}</h1>
                        </div>
                        <a href="{{ route('alumno.cursos.show', $curso->id) }}" class="btn btn-light mt-2 mt-md-0">
                            <i class="fas fa-arrow-left me-1"></i>Volver al Curso
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Mensajes Flash --}}
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        {{-- Columna Principal: Instrucciones y Entrega --}}
        <div class="col-lg-8">
            {{-- Descripción / Instrucciones --}}
            <div class="card shadow-sm hover-shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2 text-primary"></i>Instrucciones
                    </h5>
                </div>
                <div class="card-body">
                    @if($tarea->descripcion)
                        <div class="text-break">{!! nl2br(e($tarea->descripcion)) !!}</div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-file-alt fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">No hay descripción adicional para esta tarea.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Sección de Entrega --}}
            <div class="card shadow-sm hover-shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="fas fa-paper-plane me-2 text-primary"></i>Tu Entrega
                    </h5>
                </div>
                <div class="card-body">
                    @if($entregaEstudiante)
                        {{-- Mostrar información de la entrega existente --}}
                        <div class="card {{ $entregaEstudiante->calificacion !== null ? 'bg-light' : 'bg-primary-soft' }} border-0">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <i class="fas fa-calendar-check me-2 text-primary"></i>
                                        <strong>Entregado el:</strong> {{ $entregaEstudiante->fecha_entrega->format('d/m/Y H:i A') }}
                                    </div>
                                    <div>
                                        @if($entregaEstudiante->estado_entrega == 'entregado_tarde')
                                            <span class="badge bg-warning text-dark">
                                                <i class="fas fa-clock me-1"></i>Entrega Tardía
                                            </span>
                                        @elseif($entregaEstudiante->estado_entrega == 'calificado')
                                            <span class="badge bg-success">
                                                <i class="fas fa-check me-1"></i>Calificado
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Contenido de la entrega --}}
                                @if($entregaEstudiante->ruta_archivo)
                                    <div class="d-grid gap-2">
                                        <a href="{{ Storage::url($entregaEstudiante->ruta_archivo) }}" 
                                           target="_blank" 
                                           class="btn btn-outline-primary">
                                            <i class="fas fa-download me-2"></i>Ver/Descargar Archivo
                                        </a>
                                    </div>
                                @elseif($entregaEstudiante->texto_entrega)
                                    <div class="card bg-white">
                                        <div class="card-body">
                                            <h6 class="text-muted mb-3">
                                                <i class="fas fa-align-left me-2"></i>Texto Entregado:
                                            </h6>
                                            <div class="text-break">
                                                <pre class="mb-0">{{ $entregaEstudiante->texto_entrega }}</pre>
                                            </div>
                                        </div>
                                    </div>
                                @elseif($entregaEstudiante->url_entrega)
                                    <div class="d-grid gap-2">
                                        <a href="{{ $entregaEstudiante->url_entrega }}" 
                                           target="_blank" 
                                           rel="noopener noreferrer" 
                                           class="btn btn-outline-primary">
                                            <i class="fas fa-external-link-alt me-2"></i>Abrir Enlace
                                        </a>
                                    </div>
                                @endif

                                {{-- Calificación y Retroalimentación --}}
                                @if($entregaEstudiante->calificacion !== null)
                                    <hr class="my-4">
                                    <div class="text-center mb-3">
                                        <h6 class="text-success mb-2">
                                            <i class="fas fa-star me-2"></i>Calificación
                                        </h6>
                                        <div class="display-6 fw-bold text-primary mb-1">
                                            {{ $entregaEstudiante->calificacion }}
                                            <small class="text-muted fs-6">/ {{ $tarea->puntos_maximos ?? 'N/A' }} pts</small>
                                        </div>
                                    </div>
                                    @if($entregaEstudiante->retroalimentacion)
                                        <div class="card bg-white">
                                            <div class="card-body">
                                                <h6 class="text-muted mb-3">
                                                    <i class="fas fa-comment-dots me-2"></i>Retroalimentación del Docente:
                                                </h6>
                                                <div class="text-break">
                                                    {!! nl2br(e($entregaEstudiante->retroalimentacion)) !!}
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <p class="text-center text-muted mb-0">
                                            <i class="fas fa-info-circle me-2"></i>No hay retroalimentación adicional.
                                        </p>
                                    @endif
                                @else
                                    <div class="text-center text-muted mt-3">
                                        <i class="fas fa-hourglass-half me-2"></i>Tu entrega está pendiente de calificación
                                    </div>
                                @endif
                            </div>
                        </div>
                    @elseif($puedeEntregar)
                        {{-- Formulario de entrega --}}
                        <form action="{{ route('alumno.cursos.tareas.storeEntrega', [$curso->id, $tarea->id]) }}" 
                              method="POST" 
                              enctype="multipart/form-data" 
                              class="card bg-light border-0">
                            @csrf
                            <div class="card-body">
                                @if($tarea->tipo_entrega == 'archivo')
                                    <div class="mb-4">
                                        <label class="form-label">
                                            <i class="fas fa-file me-2"></i>Sube tu archivo
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input class="form-control form-control-lg @error('archivo_entrega') is-invalid @enderror" 
                                               type="file" 
                                               id="archivo_entrega" 
                                               name="archivo_entrega" 
                                               required>
                                        @error('archivo_entrega')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Formatos permitidos: pdf, doc, docx, zip, rar, jpg, png, txt. Máx: 10MB
                                        </div>
                                    </div>
                                @elseif($tarea->tipo_entrega == 'texto')
                                    <div class="mb-4">
                                        <label class="form-label">
                                            <i class="fas fa-pen me-2"></i>Escribe tu respuesta
                                            <span class="text-danger">*</span>
                                        </label>
                                        <textarea class="form-control form-control-lg @error('texto_entrega') is-invalid @enderror" 
                                                  id="texto_entrega" 
                                                  name="texto_entrega" 
                                                  rows="10" 
                                                  required 
                                                  placeholder="Escribe aquí tu respuesta...">{{ old('texto_entrega') }}</textarea>
                                        @error('texto_entrega')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @elseif($tarea->tipo_entrega == 'url')
                                    <div class="mb-4">
                                        <label class="form-label">
                                            <i class="fas fa-link me-2"></i>Pega el enlace (URL)
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="url" 
                                               class="form-control form-control-lg @error('url_entrega') is-invalid @enderror" 
                                               id="url_entrega" 
                                               name="url_entrega" 
                                               value="{{ old('url_entrega') }}" 
                                               placeholder="https://ejemplo.com/tu-entrega" 
                                               required>
                                        @error('url_entrega')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @elseif($tarea->tipo_entrega == 'ninguno')
                                    <div class="text-center py-4">
                                        <i class="fas fa-info-circle fa-2x text-primary mb-2"></i>
                                        <p class="mb-0">Esta tarea no requiere una entrega en línea.</p>
                                    </div>
                                @endif

                                @if($tarea->tipo_entrega != 'ninguno')
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="fas fa-paper-plane me-2"></i>Enviar Entrega
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </form>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-clock fa-3x text-warning mb-3"></i>
                            <h5>Plazo de entrega finalizado</h5>
                            <p class="text-muted mb-0">Ya no es posible realizar entregas para esta tarea.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Columna Lateral: Detalles de la Tarea --}}
        <div class="col-lg-4">
            <div class="card shadow-sm hover-shadow-sm sticky-lg-top">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2 text-primary"></i>Detalles
                    </h5>
                </div>
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex align-items-center">
                        <i class="fas fa-file-signature fa-fw text-primary me-3"></i>
                        <div>
                            <small class="text-muted d-block">Tipo de Entrega</small>
                            <strong>{{ ucfirst($tarea->tipo_entrega) }}</strong>
                        </div>
                    </div>
                    <div class="list-group-item d-flex align-items-center">
                        <i class="fas fa-star fa-fw text-primary me-3"></i>
                        <div>
                            <small class="text-muted d-block">Puntos Máximos</small>
                            <strong>{{ $tarea->puntos_maximos ?? 'N/A' }}</strong>
                        </div>
                    </div>
                    <div class="list-group-item d-flex align-items-center">
                        <i class="fas fa-calendar-alt fa-fw text-primary me-3"></i>
                        <div>
                            <small class="text-muted d-block">Fecha Límite</small>
                            <strong>{{ $tarea->fecha_limite ? $tarea->fecha_limite->format('d/m/Y, H:i A') : 'Sin fecha límite' }}</strong>
                        </div>
                    </div>
                    <div class="list-group-item d-flex align-items-center">
                        @if($tarea->permite_entrega_tardia)
                            <i class="fas fa-exclamation-triangle fa-fw text-warning me-3"></i>
                            <div>
                                <small class="text-muted d-block">Entrega Tardía</small>
                                <strong>Permitida 
                                    @if($tarea->fecha_limite_tardia)
                                        hasta {{ $tarea->fecha_limite_tardia->format('d/m/Y, H:i A') }}
                                    @endif
                                </strong>
                            </div>
                        @else
                            <i class="fas fa-calendar-times fa-fw text-danger me-3"></i>
                            <div>
                                <small class="text-muted d-block">Entrega Tardía</small>
                                <strong>No permitida</strong>
                            </div>
                        @endif
                    </div>
                    @if($tarea->modulo)
                        <div class="list-group-item d-flex align-items-center">
                            <i class="fas fa-sitemap fa-fw text-primary me-3"></i>
                            <div>
                                <small class="text-muted d-block">Módulo</small>
                                <strong>{{ $tarea->modulo->titulo }}</strong>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hover-shadow-sm {
    transition: all 0.2s ease-in-out;
}
.hover-shadow-sm:hover {
    transform: translateY(-2px);
    box-shadow: 0 .3rem .5rem rgba(0,0,0,.08)!important;
}
.bg-primary-soft {
    background-color: rgba(13, 110, 253, 0.1);
    border-color: rgba(13, 110, 253, 0.2);
}
.text-break pre {
    white-space: pre-wrap;
    word-wrap: break-word;
    margin-bottom: 0;
    font-family: inherit;
}
.sticky-lg-top {
    top: 1rem;
}
@media (max-width: 991.98px) {
    .sticky-lg-top {
        position: static;
    }
}
</style>
@endsection
