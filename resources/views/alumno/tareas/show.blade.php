@extends('layouts.app')

@section('content')
<div class="container">
    {{-- Navegación para volver al curso --}}
    <div class="mb-3">
        <a href="{{ route('alumno.cursos.show', $curso->id) }}" class="btn btn-secondary btn-sm">&laquo; Volver al Curso: {{ $curso->titulo }}</a>
    </div>

    {{-- Título de la Tarea --}}
    <h1>{{ $tarea->titulo }}</h1>
    <hr>

    {{-- Mensajes de estado/error --}}
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            {{-- Descripción / Instrucciones --}}
            <h5>Instrucciones:</h5>
            @if($tarea->descripcion)
                <div class="mb-4">{!! nl2br(e($tarea->descripcion)) !!}</div>
            @else
                <p class="text-muted">No hay descripción adicional.</p>
            @endif

             {{-- Sección de Entrega --}}
            <div class="card mt-4">
                <div class="card-header">
                    <h5>Tu Entrega</h5>
                </div>
                <div class="card-body">
                    @if($entregaEstudiante)
                        {{-- Mostrar información de la entrega existente --}}
                        {{-- Cambiado alert-info a alert-light para menos énfasis si está calificado --}}
                        <div class="alert {{ $entregaEstudiante->calificacion !== null ? 'alert-light border' : 'alert-info' }}">
                            <p class="mb-1"><strong>Entregado el:</strong> {{ $entregaEstudiante->fecha_entrega->format('d/m/Y H:i') }}
                               @if($entregaEstudiante->estado_entrega == 'entregado_tarde') <span class="badge bg-warning text-dark">Tarde</span> @endif
                            </p>
                            {{-- Mostrar contenido entregado --}}
                            @if($entregaEstudiante->ruta_archivo)
                                <p class="mb-1"><strong>Archivo:</strong> <a href="{{ Storage::url($entregaEstudiante->ruta_archivo) }}" target="_blank">Ver/Descargar archivo entregado</a></p>
                            @elseif($entregaEstudiante->texto_entrega)
                                <p class="mb-1"><strong>Texto:</strong></p>
                                <div class="p-2 bg-light border rounded mb-2"><pre style="white-space: pre-wrap; word-wrap: break-word;">{{ $entregaEstudiante->texto_entrega }}</pre></div>
                            @elseif($entregaEstudiante->url_entrega)
                                <p class="mb-1"><strong>Enlace:</strong> <a href="{{ $entregaEstudiante->url_entrega }}" target="_blank" rel="noopener noreferrer">{{ $entregaEstudiante->url_entrega }}</a></p>
                            @endif

                            {{-- Mostrar Calificación y Retroalimentación si existen --}}
                            @if($entregaEstudiante->calificacion !== null)
                                <hr>
                                <p class="mb-1"><strong>Calificación:</strong> <span class="fw-bold fs-5">{{ $entregaEstudiante->calificacion }}</span> / {{ $tarea->puntos_maximos ?? 'N/A' }}</p>
                                @if($entregaEstudiante->retroalimentacion)
                                    <p class="mb-1"><strong>Retroalimentación del Docente:</strong></p>
                                    {{-- Usamos un div con estilo para destacar la retroalimentación --}}
                                    <div class="p-3 bg-white border rounded shadow-sm">{!! nl2br(e($entregaEstudiante->retroalimentacion)) !!}</div>
                                @else
                                     <p class="mb-0 mt-2"><small><em>No hay retroalimentación adicional.</em></small></p>
                                @endif
                            @else
                                <p class="mb-0 mt-2"><em>Aún no ha sido calificada.</em></p>
                            @endif
                            {{-- Aquí podrías añadir lógica para permitir editar la entrega si $puedeEntregar es true y aún no está calificada, etc. --}}
                        </div>
                    @elseif($puedeEntregar)
                        {{-- Si no hay entrega Y puede entregar, mostrar el formulario --}}
                        <form action="{{ route('alumno.cursos.tareas.storeEntrega', [$curso->id, $tarea->id]) }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            {{-- Mostrar campo según el tipo de entrega de la tarea --}}
                            @if($tarea->tipo_entrega == 'archivo')
                                <div class="mb-3">
                                    <label for="archivo_entrega" class="form-label">Sube tu archivo <span class="text-danger">*</span></label>
                                    <input class="form-control @error('archivo_entrega') is-invalid @enderror" type="file" id="archivo_entrega" name="archivo_entrega" required>
                                    @error('archivo_entrega')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Tipos permitidos: pdf, doc, docx, zip, rar, jpg, png. Máx: 10MB.</div>
                                </div>
                            @elseif($tarea->tipo_entrega == 'texto')
                                <div class="mb-3">
                                     <label for="texto_entrega" class="form-label">Escribe tu respuesta <span class="text-danger">*</span></label>
                                     <textarea class="form-control @error('texto_entrega') is-invalid @enderror" id="texto_entrega" name="texto_entrega" rows="8" required>{{ old('texto_entrega') }}</textarea>
                                     @error('texto_entrega')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @elseif($tarea->tipo_entrega == 'url')
                                 <div class="mb-3">
                                    <label for="url_entrega" class="form-label">Pega el enlace (URL) <span class="text-danger">*</span></label>
                                    <input type="url" class="form-control @error('url_entrega') is-invalid @enderror" id="url_entrega" name="url_entrega" value="{{ old('url_entrega') }}" placeholder="https://..." required>
                                     @error('url_entrega')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @elseif($tarea->tipo_entrega == 'ninguno')
                                 <div class="alert alert-secondary">Esta tarea no requiere una entrega en línea.</div>
                            @endif

                            {{-- Botón de envío (solo si el tipo de entrega no es 'ninguno') --}}
                            @if($tarea->tipo_entrega != 'ninguno')
                                <button type="submit" class="btn btn-primary">Enviar Entrega</button>
                            @endif
                        </form>
                    @else
                        {{-- Si no hay entrega Y NO puede entregar (plazo vencido) --}}
                         <div class="alert alert-warning">
                            El plazo para entregar esta tarea ha finalizado.
                         </div>
                    @endif
                </div>
            </div>

        </div>
        <div class="col-md-4">
            {{-- Información Adicional de la Tarea --}}
            <div class="card">
                <div class="card-header">Detalles</div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><strong>Tipo de Entrega:</strong> {{ ucfirst($tarea->tipo_entrega) }}</li>
                    <li class="list-group-item"><strong>Puntos Máximos:</strong> {{ $tarea->puntos_maximos ?? 'N/A' }}</li>
                    <li class="list-group-item"><strong>Fecha Límite:</strong> {{ $tarea->fecha_limite ? $tarea->fecha_limite->format('d/m/Y H:i') : 'Sin fecha límite' }}</li>
                    @if($tarea->permite_entrega_tardia)
                         <li class="list-group-item"><strong>Permite Tardía:</strong> Sí @if($tarea->fecha_limite_tardia) (hasta {{ $tarea->fecha_limite_tardia->format('d/m/Y H:i') }}) @endif</li>
                    @else
                         <li class="list-group-item"><strong>Permite Tardía:</strong> No</li>
                    @endif
                     @if($tarea->modulo)
                        <li class="list-group-item"><strong>Módulo:</strong> {{ optional($tarea->modulo)->titulo }}</li>
                    @endif
                </ul>
            </div>
        </div>
    </div>

</div>
@endsection
