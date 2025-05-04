{{-- Recibe una variable $material --}}
    <div class="mb-3 p-2 border rounded">
        <div class="d-flex align-items-center gap-2">
            {{-- Icono según tipo (ejemplo con Font Awesome - necesita configuración) --}}
            @if($material->tipo_material == 'archivo') <i class="fas fa-file-alt fa-fw"></i>
            @elseif($material->tipo_material == 'enlace') <i class="fas fa-link fa-fw"></i>
            @elseif($material->tipo_material == 'video') <i class="fas fa-video fa-fw"></i>
            @elseif($material->tipo_material == 'texto') <i class="fas fa-align-left fa-fw"></i>
            @endif

            <strong>{{ $material->titulo }}</strong>
        </div>

        @if($material->descripcion)
            <p class="mb-1 small text-muted">{{ $material->descripcion }}</p>
        @endif

        {{-- Enlace/Acción según tipo --}}
        @if($material->tipo_material == 'enlace' || $material->tipo_material == 'video')
            <a href="{{ $material->enlace_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary mt-1">Abrir Enlace</a>
        @elseif($material->tipo_material == 'archivo' && $material->ruta_archivo)
             <a href="{{ Storage::url($material->ruta_archivo) }}" target="_blank" class="btn btn-sm btn-outline-primary mt-1">Ver/Descargar Archivo</a>
        @elseif($material->tipo_material == 'texto' && $material->contenido_texto)
             {{-- Podríamos mostrar el texto directamente o en un modal --}}
             {{-- Por ahora, un simple indicador --}}
             <span class="badge bg-light text-dark">Contenido de Texto</span>
             {{-- O mostrar directamente si es corto: --}}
             {{-- <div class="mt-2 p-2 bg-light border rounded small">{!! nl2br(e($material->contenido_texto)) !!}</div> --}}
        @endif
    </div>
    