{{-- Recibe una variable $material --}}
<div class="card shadow-sm hover-shadow-sm mb-2">
    <div class="card-body py-2 px-3">
        <div class="d-flex align-items-center gap-3">
            {{-- Icono según tipo con colores --}}
            <div class="icon-wrapper rounded-circle p-2 d-flex align-items-center justify-content-center 
                @if($material->tipo_material == 'archivo') bg-primary-light text-primary
                @elseif($material->tipo_material == 'enlace') bg-info-light text-info
                @elseif($material->tipo_material == 'video') bg-danger-light text-danger
                @else bg-secondary-light text-secondary
                @endif">
                @if($material->tipo_material == 'archivo') <i class="fas fa-file-alt"></i>
                @elseif($material->tipo_material == 'enlace') <i class="fas fa-link"></i>
                @elseif($material->tipo_material == 'video') <i class="fas fa-video"></i>
                @elseif($material->tipo_material == 'texto') <i class="fas fa-align-left"></i>
                @endif
            </div>

            <div class="flex-grow-1">
                <h6 class="mb-1 fw-bold">{{ $material->titulo }}</h6>
                @if($material->descripcion)
                    <p class="mb-2 small text-muted">{{ Str::limit($material->descripcion, 100) }}</p>
                @endif

                {{-- Botones de acción según tipo --}}
                <div class="action-buttons">
                    @if($material->tipo_material == 'enlace' || $material->tipo_material == 'video')
                        <a href="{{ $material->enlace_url }}" target="_blank" rel="noopener noreferrer" 
                           class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-external-link-alt me-1"></i>Abrir Enlace
                        </a>
                    @elseif($material->tipo_material == 'archivo' && $material->ruta_archivo)
                        <a href="{{ Storage::url($material->ruta_archivo) }}" target="_blank" 
                           class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-download me-1"></i>Ver/Descargar
                        </a>
                    @elseif($material->tipo_material == 'texto' && $material->contenido_texto)
                        <button type="button" class="btn btn-sm btn-outline-secondary" 
                                data-bs-toggle="collapse" 
                                data-bs-target="#texto-{{ $material->id }}">
                            <i class="fas fa-eye me-1"></i>Ver Contenido
                        </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- Contenedor colapsable para texto --}}
        @if($material->tipo_material == 'texto' && $material->contenido_texto)
            <div class="collapse mt-3" id="texto-{{ $material->id }}">
                <div class="p-3 bg-light border rounded">
                    {!! nl2br(e($material->contenido_texto)) !!}
                </div>
            </div>
        @endif
    </div>
</div>

<style>
.icon-wrapper {
    width: 40px;
    height: 40px;
}
.bg-primary-light { background-color: rgba(13, 110, 253, 0.1); }
.bg-info-light { background-color: rgba(13, 202, 240, 0.1); }
.bg-danger-light { background-color: rgba(220, 53, 69, 0.1); }
.bg-secondary-light { background-color: rgba(108, 117, 125, 0.1); }
.hover-shadow-sm {
    transition: all 0.2s ease-in-out;
}
.hover-shadow-sm:hover {
    transform: translateY(-1px);
    box-shadow: 0 .125rem .25rem rgba(0,0,0,.075)!important;
}
</style>
