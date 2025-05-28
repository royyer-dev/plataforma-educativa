@extends('layouts.app')

@section('content')
<div class="container py-4">
    {{-- Encabezado de la carrera --}}
    <div class="card bg-primary text-white mb-4">
        <div class="card-body py-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="display-6 mb-2">{{ $carrera->nombre }}</h1>
                    @if($carrera->descripcion)
                        <p class="mb-0 opacity-75">{{ $carrera->descripcion }}</p>
                    @endif                </div>
                <div class="text-end">
                    <div class="h4 mb-0">{{ $cursosDeLaCarrera->count() }}</div>
                    <small class="opacity-75">Cursos disponibles</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Mensajes Flash --}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Filtros y búsqueda --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0">
                    <i class="fas fa-search text-muted"></i>
                </span>
                <input type="text" class="form-control border-start-0" id="searchCurso" 
                       placeholder="Buscar cursos...">
            </div>
        </div>
        <div class="col-md-6">
            <div class="btn-group w-100">
                <button type="button" class="btn btn-outline-primary active" data-filter="todos">
                    Todos
                </button>
                <button type="button" class="btn btn-outline-primary" data-filter="disponibles">
                    Disponibles
                </button>
                <button type="button" class="btn btn-outline-primary" data-filter="inscritos">
                    Inscritos
                </button>
            </div>
        </div>
    </div>    {{-- Lista de cursos --}}
    @if($cursosDeLaCarrera->count() > 0)
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
            @foreach($cursosDeLaCarrera as $curso)
                <div class="col curso-item">
                    <div class="card h-100 shadow-sm hover-shadow-lg">
                        {{-- Cabecera del curso con imagen/icono --}}
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center py-4">
                            <i class="fas fa-book-reader fa-3x text-primary opacity-50"></i>
                        </div>
                        
                        <div class="card-body d-flex flex-column">
                            {{-- Título y estado --}}                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title mb-0">{{ $curso->titulo }}</h5>
                                @if(isset($inscripcionesEstudiante[$curso->id]))
                                    @php
                                        $inscripcion = $inscripcionesEstudiante[$curso->id];
                                    @endphp
                                    @if($inscripcion->estado === 'pendiente')
                                        <span class="badge bg-warning">Pendiente</span>
                                    @elseif($inscripcion->estado === 'aprobada')
                                        <span class="badge bg-success">Inscrito</span>
                                    @elseif($inscripcion->estado === 'rechazada')
                                        <span class="badge bg-danger">Rechazada</span>
                                    @endif
                                @else
                                    <span class="badge bg-primary">Disponible</span>
                                @endif
                            </div>

                            {{-- Información del curso --}}
                            <div class="mb-3">
                                @if($curso->descripcion)
                                    <p class="card-text text-muted small">{{ Str::limit($curso->descripcion, 100) }}</p>
                                @endif
                                <div class="small text-muted">
                                    <p class="mb-1">
                                        <i class="fas fa-user-tie me-2"></i>
                                        @if($curso->profesores && $curso->profesores->count() > 0)
                                            {{ $curso->profesores->pluck('nombre')->implode(', ') }}
                                        @else
                                            Profesor no asignado
                                        @endif
                                    </p>
                                    @if($curso->fecha_inicio)
                                        <p class="mb-1">
                                            <i class="fas fa-calendar-alt me-2"></i>
                                            Inicia: {{ $curso->fecha_inicio->format('d/m/Y') }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            {{-- Botones de acción --}}                            <div class="mt-auto">
                                @if(isset($inscripcionesEstudiante[$curso->id]))
                                    @php
                                        $inscripcion = $inscripcionesEstudiante[$curso->id];
                                    @endphp
                                    @if($inscripcion->estado === 'aprobada')
                                        <a href="{{ route('alumno.cursos.show', $curso->id) }}" 
                                           class="btn btn-primary w-100">
                                            <i class="fas fa-eye me-1"></i>Ver Curso
                                        </a>
                                    @elseif($inscripcion->estado === 'pendiente')
                                        <button class="btn btn-warning w-100" disabled>
                                            <i class="fas fa-clock me-1"></i>Pendiente de Aprobación
                                        </button>
                                    @else
                                        <form action="{{ route('alumno.cursos.solicitar-inscripcion', $curso->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-primary w-100">
                                                <i class="fas fa-user-plus me-1"></i>Solicitar Inscripción
                                            </button>
                                        </form>
                                    @endif
                                @else
                                    <form action="{{ route('alumno.cursos.solicitar-inscripcion', $curso->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-primary w-100">
                                            <i class="fas fa-user-plus me-1"></i>Solicitar Inscripción
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-light text-center border shadow-sm p-4">
            <i class="fas fa-info-circle fa-2x text-primary mb-2"></i>
            <p class="mb-1 fs-5">No hay cursos disponibles en esta carrera.</p>
            <small class="text-muted">Por favor, vuelve más tarde o explora otras carreras.</small>
        </div>
    @endif
</div>

<style>
.hover-shadow-lg {
    transition: box-shadow .2s ease-in-out, transform .2s ease-in-out;
}
.hover-shadow-lg:hover {
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    transform: translateY(-3px);
}
.card-img-top {
    height: 160px;
}
.badge {
    font-weight: 500;
    padding: 0.5em 0.8em;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Búsqueda de cursos
    const searchInput = document.getElementById('searchCurso');
    const cursoItems = document.querySelectorAll('.curso-item');

    searchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        
        cursoItems.forEach(item => {
            const titulo = item.querySelector('.card-title').textContent.toLowerCase();
            const descripcion = item.querySelector('.card-text')?.textContent.toLowerCase() || '';
            const profesor = item.querySelector('.fas.fa-user-tie').nextSibling.textContent.toLowerCase();
            
            if (titulo.includes(searchTerm) || 
                descripcion.includes(searchTerm) || 
                profesor.includes(searchTerm)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    });

    // Filtros
    const filterButtons = document.querySelectorAll('[data-filter]');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Actualizar estados de botones
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            const filter = this.dataset.filter;
            
            cursoItems.forEach(item => {
                const badge = item.querySelector('.badge');
                if (filter === 'todos') {
                    item.style.display = '';
                } else if (filter === 'disponibles') {
                    item.style.display = badge.classList.contains('bg-primary') ? '' : 'none';
                } else if (filter === 'inscritos') {
                    item.style.display = badge.classList.contains('bg-success') ? '' : 'none';
                }
            });
        });
    });
});
</script>
@endsection
