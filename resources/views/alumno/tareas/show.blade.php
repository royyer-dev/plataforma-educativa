@extends('layouts.app')

@section('content')
<div class="container py-4">

    {{-- Breadcrumbs y Navegación --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('alumno.dashboard') }}">Dashboard</a></li>
            @if(isset($curso) && optional($curso->carrera)->id)
                <li class="breadcrumb-item"><a href="{{ route('alumno.carreras.index') }}">Carreras</a></li>
                <li class="breadcrumb-item"><a href="{{ route('alumno.cursos.index', ['carrera' => $curso->carrera->id]) }}">{{ Str::limit(optional($curso->carrera)->nombre, 25) }}</a></li>
            @endif
            <li class="breadcrumb-item"><a href="{{ route('alumno.cursos.show', $curso->id) }}">{{ Str::limit($curso->titulo, 30) }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tarea</li>
        </ol>
    </nav>

    {{-- Título de la Tarea --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <h1><i class="fas fa-clipboard-list me-2 text-primary"></i>{{ $tarea->titulo }}</h1>
        <a href="{{ route('alumno.cursos.show', $curso->id) }}" class="btn btn-outline-secondary btn-sm mt-2 mt-md-0">
            <i class="fas fa-arrow-left me-1"></i> Volver al Curso
        </a>
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

    <div class="row">
        {{-- Columna Principal: Instrucciones y Entrega --}}
        <div class="col-lg-8 mb-4 mb-lg-0">
            {{-- Descripción / Instrucciones --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2 text-info"></i>Instrucciones</h5>
                </div>
                <div class="card-body">
                    @if($tarea->descripcion)
                        <div class="text-break">{!! nl2br(e($tarea->descripcion)) !!}</div>
                    @else
                        <p class="text-muted fst-italic">No hay descripción adicional para esta tarea.</p>
                    @endif
                </div>
            </div>

            {{-- Sección de Entrega --}}
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-paper-plane me-2 text-success"></i>Tu Entrega</h5>
                </div>
                <div class="card-body">
                    @if($entregaEstudiante)
                        {{-- Mostrar información de la entrega existente --}}
                        <div class="p-3 rounded border {{ $entregaEstudiante->calificacion !== null ? 'bg-light' : 'bg-primary-soft' }}">
                            <p class="mb-1">
                                <i class="fas fa-calendar-check me-1 text-muted"></i><strong>Entregado el:</strong>
                                {{ $entregaEstudiante->fecha_entrega->format('d/m/Y H:i A') }}
                                @if($entregaEstudiante->estado_entrega == 'entregado_tarde')
                                    <span class="badge bg-warning text-dark rounded-pill ms-2">Tarde</span>
                                @elseif($entregaEstudiante->estado_entrega == 'calificado')
                                    <span class="badge bg-success rounded-pill ms-2">Calificado</span>
                                @endif
                            </p>

                            {{-- Mostrar contenido entregado --}}
                            @if($entregaEstudiante->ruta_archivo)
                                <p class="mb-1 mt-2">
                                    <i class="fas fa-file-alt me-1 text-muted"></i><strong>Archivo:</strong>
                                    <a href="{{ Storage::url($entregaEstudiante->ruta_archivo) }}" target="_blank" class="btn btn-sm btn-outline-secondary ms-2">
                                        <i class="fas fa-download me-1"></i> Ver/Descargar Archivo
                                    </a>
                                </p>
                            @elseif($entregaEstudiante->texto_entrega)
                                <p class="mb-1 mt-2"><i class="fas fa-align-left me-1 text-muted"></i><strong>Texto Entregado:</strong></p>
                                <div class="p-3 bg-white border rounded mb-2 text-break" style="max-height: 200px; overflow-y: auto;"><pre class="mb-0" style="white-space: pre-wrap; word-wrap: break-word;">{{ $entregaEstudiante->texto_entrega }}</pre></div>
                            @elseif($entregaEstudiante->url_entrega)
                                <p class="mb-1 mt-2">
                                    <i class="fas fa-link me-1 text-muted"></i><strong>Enlace:</strong>
                                    <a href="{{ $entregaEstudiante->url_entrega }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-info ms-2">
                                        <i class="fas fa-external-link-alt me-1"></i> Abrir Enlace
                                    </a>
                                </p>
                            @endif

                            {{-- Mostrar Calificación y Retroalimentación si existen --}}
                            @if($entregaEstudiante->calificacion !== null)
                                <hr class="my-3">
                                <h6 class="text-success"><i class="fas fa-check-circle me-1"></i>Calificación</h6>
                                <p class="mb-1 fs-4">
                                    <span class="fw-bold">{{ $entregaEstudiante->calificacion }}</span> / <small class="text-muted">{{ $tarea->puntos_maximos ?? 'N/A' }} pts</small>
                                </p>
                                @if($entregaEstudiante->retroalimentacion)
                                    <p class="mb-1 mt-2"><strong><i class="fas fa-comment-dots me-1 text-muted"></i>Retroalimentación del Docente:</strong></p>
                                    <div class="p-3 bg-white border rounded text-break">{!! nl2br(e($entregaEstudiante->retroalimentacion)) !!}</div>
                                @else
                                     <p class="mb-0 mt-2 small fst-italic text-muted">No hay retroalimentación adicional.</p>
                                @endif
                            @else
                                <p class="mb-0 mt-3 fst-italic text-muted"><i class="fas fa-hourglass-half me-1"></i>Aún no ha sido calificada.</p>
                            @endif
                        </div>
                    @elseif($puedeEntregar)
                        {{-- Si no hay entrega Y puede entregar, mostrar el formulario --}}
                        <form action="{{ route('alumno.cursos.tareas.storeEntrega', [$curso->id, $tarea->id]) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @if($tarea->tipo_entrega == 'archivo')
                                <div class="mb-3">
                                    <label for="archivo_entrega" class="form-label fw-bold">Sube tu archivo <span class="text-danger">*</span></label>
                                    <input class="form-control @error('archivo_entrega') is-invalid @enderror" type="file" id="archivo_entrega" name="archivo_entrega" required>
                                    @error('archivo_entrega')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Tipos permitidos: pdf, doc, docx, zip, rar, jpg, png, txt. Máx: 10MB.</div>
                                </div>
                            @elseif($tarea->tipo_entrega == 'texto')
                                <div class="mb-3">
                                     <label for="texto_entrega" class="form-label fw-bold">Escribe tu respuesta <span class="text-danger">*</span></label>
                                     <textarea class="form-control @error('texto_entrega') is-invalid @enderror" id="texto_entrega" name="texto_entrega" rows="10" required placeholder="Escribe aquí tu respuesta...">{{ old('texto_entrega') }}</textarea>
                                     @error('texto_entrega')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @elseif($tarea->tipo_entrega == 'url')
                                 <div class="mb-3">
                                    <label for="url_entrega" class="form-label fw-bold">Pega el enlace (URL) <span class="text-danger">*</span></label>
                                    <input type="url" class="form-control @error('url_entrega') is-invalid @enderror" id="url_entrega" name="url_entrega" value="{{ old('url_entrega') }}" placeholder="https://ejemplo.com/tu-entrega" required>
                                     @error('url_entrega')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @elseif($tarea->tipo_entrega == 'ninguno')
                                 <div class="alert alert-secondary text-center" role="alert">
                                    <i class="fas fa-info-circle me-1"></i>Esta tarea no requiere una entrega en línea.
                                 </div>
                            @endif

                            @if($tarea->tipo_entrega != 'ninguno')
                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-paper-plane me-1"></i> Enviar Entrega
                                </button>
                            @endif
                        </form>
                    @else
                         <div class="alert alert-warning text-center" role="alert">
                            <i class="fas fa-clock me-2"></i>El plazo para entregar esta tarea ha finalizado.
                         </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Columna Lateral: Detalles de la Tarea --}}
        <div class="col-lg-4">
            <div class="card shadow-sm sticky-lg-top"> {{-- sticky-lg-top para que se quede fija en pantallas grandes --}}
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Detalles de la Tarea</h5>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <i class="fas fa-file-signature fa-fw text-muted me-2"></i><strong>Tipo de Entrega:</strong> {{ ucfirst($tarea->tipo_entrega) }}
                    </li>
                    <li class="list-group-item">
                        <i class="fas fa-star fa-fw text-muted me-2"></i><strong>Puntos Máximos:</strong> {{ $tarea->puntos_maximos ?? 'N/A' }}
                    </li>
                    <li class="list-group-item">
                        <i class="fas fa-calendar-alt fa-fw text-muted me-2"></i><strong>Fecha Límite:</strong>
                        <span class="fw-bold">{{ $tarea->fecha_limite ? $tarea->fecha_limite->format('d/m/Y, H:i A') : 'Sin fecha límite' }}</span>
                    </li>
                    @if($tarea->permite_entrega_tardia)
                         <li class="list-group-item">
                            <i class="fas fa-exclamation-triangle fa-fw text-warning me-2"></i><strong>Permite Tardía:</strong> Sí
                            @if($tarea->fecha_limite_tardia)
                                (hasta {{ $tarea->fecha_limite_tardia->format('d/m/Y, H:i A') }})
                            @endif
                        </li>
                    @else
                         <li class="list-group-item">
                            <i class="fas fa-calendar-times fa-fw text-muted me-2"></i><strong>Permite Tardía:</strong> No
                        </li>
                    @endif
                     @if($tarea->modulo)
                        <li class="list-group-item">
                            <i class="fas fa-sitemap fa-fw text-muted me-2"></i><strong>Módulo:</strong> {{ optional($tarea->modulo)->titulo }}
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>

</div>

{{-- Estilo para un fondo suave en la tarjeta de entrega --}}
<style>
    .bg-primary-soft {
        background-color: #cfe2ff; /* Un azul claro de Bootstrap */
        border-color: #b6d4fe;
    }
    .text-break pre { /* Para que el pre respete el text-break del div padre */
        white-space: pre-wrap;
        word-wrap: break-word;
        margin-bottom: 0;
    }
</style>
@endsection
