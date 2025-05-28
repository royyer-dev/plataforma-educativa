@extends('layouts.app')

@section('content')
<div class="container py-4">
    {{-- Encabezado del curso --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="display-6 mb-2">{{ $curso->nombre }}</h1>
                            <p class="mb-0 opacity-75">
                                <i class="fas fa-user-tie me-2"></i>{{ $curso->docente->nombre }}
                            </p>
                        </div>
                        <div class="text-end">
                            <div class="h4 mb-0">{{ $curso->codigo }}</div>
                            <small class="opacity-75">Código del curso</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Contenido principal --}}
    <div class="row">
        {{-- Sección izquierda: Materiales y recursos --}}
        <div class="col-lg-8 mb-4 mb-lg-0">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <ul class="nav nav-tabs card-header-tabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#materiales" type="button">
                                <i class="fas fa-book-open me-2"></i>Materiales
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tareas" type="button">
                                <i class="fas fa-tasks me-2"></i>Tareas
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body p-4">
                    <div class="tab-content">
                        {{-- Tab de Materiales --}}
                        <div class="tab-pane fade show active" id="materiales">
                            @if($curso->materiales->count() > 0)
                                @foreach($curso->materiales as $material)
                                    @include('alumno.cursos._material_item', ['material' => $material])
                                @endforeach
                            @else
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-folder-open fa-3x mb-3"></i>
                                    <p>No hay materiales disponibles en este momento.</p>
                                </div>
                            @endif
                        </div>

                        {{-- Tab de Tareas --}}
                        <div class="tab-pane fade" id="tareas">
                            @if($curso->tareas->count() > 0)
                                @foreach($curso->tareas as $tarea)
                                    @include('alumno.cursos._tarea_item', ['tarea' => $tarea])
                                @endforeach
                            @else
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-clipboard-list fa-3x mb-3"></i>
                                    <p>No hay tareas asignadas en este momento.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sección derecha: Información y estadísticas --}}
        <div class="col-lg-4">
            {{-- Progreso del curso --}}
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-chart-pie me-2"></i>Progreso del Curso
                    </h5>
                    @php
                        $totalTareas = $curso->tareas->count();
                        $tareasCompletadas = $curso->tareas->where('estado', 'calificada')->count();
                        $promedioCalificaciones = $curso->tareas->where('estado', 'calificada')
                            ->avg('calificacion') ?? 0;
                    @endphp
                    
                    <div class="progress mb-3" style="height: 10px;">
                        <div class="progress-bar bg-success" role="progressbar" 
                             style="width: {{ $totalTareas > 0 ? ($tareasCompletadas / $totalTareas * 100) : 0 }}%">
                        </div>
                    </div>
                    
                    <div class="row text-center">
                        <div class="col-6 border-end">
                            <div class="h4 mb-0">{{ $tareasCompletadas }}/{{ $totalTareas }}</div>
                            <small class="text-muted">Tareas Completadas</small>
                        </div>
                        <div class="col-6">
                            <div class="h4 mb-0">{{ number_format($promedioCalificaciones, 1) }}</div>
                            <small class="text-muted">Promedio</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Próximas entregas --}}
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-calendar-alt me-2"></i>Próximas Entregas
                    </h5>
                    @php
                        $proximasTareas = $curso->tareas
                            ->where('estado', 'pendiente')
                            ->where('fecha_limite', '>=', now())
                            ->sortBy('fecha_limite')
                            ->take(3);
                    @endphp

                    @if($proximasTareas->count() > 0)
                        @foreach($proximasTareas as $tarea)
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-shrink-0">
                                    <div class="calendar-date text-center border rounded p-1" style="width: 45px;">
                                        <div class="small text-uppercase">{{ $tarea->fecha_limite->format('M') }}</div>
                                        <div class="h5 mb-0">{{ $tarea->fecha_limite->format('d') }}</div>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">{{ Str::limit($tarea->titulo, 30) }}</h6>
                                    <small class="text-muted">
                                        {{ $tarea->fecha_limite->format('H:i') }} hrs
                                    </small>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted mb-0">No hay entregas pendientes próximas.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.nav-tabs .nav-link {
    color: #6c757d;
    border: none;
    padding: 0.5rem 1rem;
}
.nav-tabs .nav-link.active {
    color: #0d6efd;
    border-bottom: 2px solid #0d6efd;
    background: none;
}
.calendar-date {
    background-color: #f8f9fa;
}
.progress {
    background-color: #e9ecef;
}
</style>
@endsection
