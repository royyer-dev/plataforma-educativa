@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body py-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h1 class="display-6 mb-0"><i class="fas fa-calendar-alt me-2"></i>Mi Agenda de Tareas</h1>
                            <p class="mb-0 mt-2 opacity-75">Organiza tus entregas y mantén el control de tus tareas</p>
                        </div>
                        <a href="{{ route('alumno.dashboard') }}" class="btn btn-light mt-2 mt-md-0">
                            <i class="fas fa-arrow-left me-1"></i>Volver al Dashboard
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

    <div class="row g-4">
        {{-- SECCIÓN: TAREAS PRÓXIMAS A VENCER --}}
        <div class="col-12 col-lg-6">
            <div class="card shadow-sm hover-shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="fas fa-hourglass-start me-2 text-primary"></i>Tareas Próximas
                    </h5>
                </div>
                @if(isset($proximasTareas) && $proximasTareas->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($proximasTareas as $tarea)
                            <div class="list-group-item list-group-item-action">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0">
                                        <a href="{{ route('alumno.cursos.tareas.show', ['curso' => $tarea->curso->id, 'tarea' => $tarea->id]) }}" 
                                           class="text-decoration-none text-dark stretched-link">
                                            {{ $tarea->titulo }}
                                        </a>
                                    </h6>
                                    <span class="badge bg-danger">{{ $tarea->fecha_limite->diffForHumans() }}</span>
                                </div>
                                <div class="d-flex gap-3 small text-muted">
                                    <span><i class="fas fa-book-open me-1"></i>{{ $tarea->curso->titulo }}</span>
                                    <span><i class="fas fa-clock me-1"></i>{{ $tarea->fecha_limite->format('d/m/Y H:i A') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="card-body text-center py-5">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <h6 class="mb-2">¡Todo en orden!</h6>
                        <p class="text-muted mb-0">No tienes tareas próximas a vencer sin entregar.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- SECCIÓN: TAREAS VENCIDAS SIN ENTREGAR --}}
        <div class="col-12 col-lg-6">
            <div class="card shadow-sm hover-shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2 text-warning"></i>Vencidas Sin Entregar
                    </h5>
                </div>
                @if(isset($vencidasSinEntregar) && $vencidasSinEntregar->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($vencidasSinEntregar as $tarea)
                            <div class="list-group-item list-group-item-action">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0">
                                        <a href="{{ route('alumno.cursos.tareas.show', ['curso' => $tarea->curso->id, 'tarea' => $tarea->id]) }}" 
                                           class="text-decoration-none text-dark stretched-link">
                                            {{ $tarea->titulo }}
                                        </a>
                                    </h6>
                                    <span class="badge bg-danger">Venció {{ $tarea->fecha_limite->diffForHumans() }}</span>
                                </div>
                                <div class="d-flex gap-3 small text-muted">
                                    <span><i class="fas fa-book-open me-1"></i>{{ $tarea->curso->titulo }}</span>
                                    <span><i class="fas fa-clock me-1"></i>{{ $tarea->fecha_limite->format('d/m/Y H:i A') }}</span>
                                </div>
                                @if($tarea->permite_entrega_tardia && (!$tarea->fecha_limite_tardia || \Carbon\Carbon::now()->lte($tarea->fecha_limite_tardia)))
                                    <div class="mt-2">
                                        <span class="badge bg-info-light text-info">
                                            <i class="fas fa-info-circle me-1"></i>Aún puedes entregarla (tardía)
                                        </span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="card-body text-center py-5">
                        <i class="fas fa-thumbs-up fa-3x text-secondary mb-3"></i>
                        <h6 class="mb-2">¡Excelente!</h6>
                        <p class="text-muted mb-0">No tienes tareas vencidas pendientes de entrega.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- SECCIÓN: MIS ENTREGAS RECIENTES --}}
        <div class="col-12">
            <div class="card shadow-sm hover-shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2 text-success"></i>Entregas Recientes
                    </h5>
                </div>
                @if(isset($entregadasRecientemente) && $entregadasRecientemente->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($entregadasRecientemente as $entrega)
                            <div class="list-group-item list-group-item-action">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0">
                                        <a href="{{ route('alumno.cursos.tareas.show', ['curso' => $entrega->tarea->curso->id, 'tarea' => $entrega->tarea->id]) }}" 
                                           class="text-decoration-none text-dark stretched-link">
                                            {{ $entrega->tarea->titulo }}
                                        </a>
                                    </h6>
                                    <span class="badge bg-light text-dark border">{{ $entrega->updated_at->diffForHumans() }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex gap-3 small text-muted">
                                        <span><i class="fas fa-book-open me-1"></i>{{ $entrega->tarea->curso->titulo }}</span>
                                    </div>
                                    <div>
                                        @if($entrega->calificacion !== null)
                                            <span class="badge bg-success">
                                                <i class="fas fa-star me-1"></i>{{ $entrega->calificacion }} / {{ $entrega->tarea->puntos_maximos ?? 'N/A' }}
                                            </span>
                                        @else
                                            <span class="badge bg-primary">
                                                <i class="fas fa-clock me-1"></i>Pendiente Calificación
                                            </span>
                                        @endif
                                        @if($entrega->estado_entrega == 'entregado_tarde')
                                            <span class="badge bg-warning text-dark ms-1">
                                                <i class="fas fa-exclamation-circle me-1"></i>Tarde
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="card-body text-center py-5">
                        <i class="far fa-folder-open fa-3x text-secondary mb-3"></i>
                        <h6 class="mb-2">Sin entregas recientes</h6>
                        <p class="text-muted mb-0">No has realizado entregas en los últimos días.</p>
                    </div>
                @endif
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
.badge {
    font-weight: 500;
}
.badge.bg-info-light {
    color: #055160;
    background-color: #cff4fc;
    border: 1px solid #b6effb;
}
.list-group-item-action:hover {
    background-color: #f8f9fa;
}
</style>
@endsection
