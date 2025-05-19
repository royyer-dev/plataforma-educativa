    @extends('layouts.app')

    @section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-calendar-alt me-2"></i>Mi Agenda de Tareas</h1>
            <a href="{{ route('alumno.dashboard') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Volver al Dashboard
            </a>
        </div>

        {{-- Mensajes Flash --}}
        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- SECCIÓN: TAREAS PRÓXIMAS A VENCER --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-hourglass-start me-2"></i>Tareas Próximas a Vencer (No Entregadas)</h5>
            </div>
            @if(isset($proximasTareas) && $proximasTareas->count() > 0)
                <ul class="list-group list-group-flush">
                    @foreach($proximasTareas as $tarea)
                        <li class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">
                                    <a href="{{ route('alumno.cursos.tareas.show', ['curso' => $tarea->curso->id, 'tarea' => $tarea->id]) }}" class="text-decoration-none">
                                        {{ $tarea->titulo }}
                                    </a>
                                </h6>
                                <small class="text-danger fw-bold">{{ $tarea->fecha_limite->diffForHumans() }}</small>
                            </div>
                            <small class="text-muted d-block">
                                <i class="fas fa-book-open me-1"></i>Curso: {{ $tarea->curso->titulo }}
                            </small>
                            <small class="text-muted d-block">
                                <i class="fas fa-clock me-1"></i>Vence: {{ $tarea->fecha_limite->format('d/m/Y H:i A') }}
                            </small>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="card-body text-center text-muted">
                    <i class="fas fa-check-circle fa-2x mb-2 text-success"></i><br>
                    ¡Todo en orden! No tienes tareas próximas a vencer sin entregar.
                </div>
            @endif
        </div>

        {{-- SECCIÓN: TAREAS VENCIDAS SIN ENTREGAR --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Tareas Vencidas (Sin Entregar)</h5>
            </div>
            @if(isset($vencidasSinEntregar) && $vencidasSinEntregar->count() > 0)
                <ul class="list-group list-group-flush">
                    @foreach($vencidasSinEntregar as $tarea)
                        <li class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                 <h6 class="mb-1">
                                    <a href="{{ route('alumno.cursos.tareas.show', ['curso' => $tarea->curso->id, 'tarea' => $tarea->id]) }}" class="text-decoration-none">
                                        {{ $tarea->titulo }}
                                    </a>
                                </h6>
                                <small class="text-danger">Venció {{ $tarea->fecha_limite->diffForHumans() }}</small>
                            </div>
                            <small class="text-muted d-block">
                                <i class="fas fa-book-open me-1"></i>Curso: {{ $tarea->curso->titulo }}
                            </small>
                            <small class="text-muted d-block">
                                <i class="fas fa-clock me-1"></i>Venció el: {{ $tarea->fecha_limite->format('d/m/Y H:i A') }}
                            </small>
                            @if($tarea->permite_entrega_tardia && (!$tarea->fecha_limite_tardia || \Carbon\Carbon::now()->lte($tarea->fecha_limite_tardia)))
                                <span class="badge bg-info-light text-info-dark mt-1">Aún puedes entregarla (tardía)</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="card-body text-center text-muted">
                     <i class="fas fa-thumbs-up fa-2x mb-2 text-secondary"></i><br>
                    No tienes tareas vencidas pendientes de entrega.
                </div>
            @endif
        </div>

        {{-- SECCIÓN: MIS ENTREGAS RECIENTES --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fas fa-history me-2"></i>Mis Entregas Recientes</h5>
            </div>
            @if(isset($entregadasRecientemente) && $entregadasRecientemente->count() > 0)
                <ul class="list-group list-group-flush">
                    @foreach($entregadasRecientemente as $entrega)
                        <li class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">
                                    <a href="{{ route('alumno.cursos.tareas.show', ['curso' => $entrega->tarea->curso->id, 'tarea' => $entrega->tarea->id]) }}" class="text-decoration-none">
                                        {{ $entrega->tarea->titulo }}
                                    </a>
                                </h6>
                                <small class="text-muted">{{ $entrega->updated_at->diffForHumans() }}</small>
                            </div>
                            <small class="text-muted d-block">
                                <i class="fas fa-book-open me-1"></i>Curso: {{ $entrega->tarea->curso->titulo }}
                            </small>
                            <small class="text-muted d-block">
                                <i class="fas fa-paperclip me-1"></i>Estado:
                                @if($entrega->calificacion !== null)
                                    <span class="badge bg-success">Calificada: {{ $entrega->calificacion }} / {{ $entrega->tarea->puntos_maximos ?? 'N/A' }}</span>
                                @else
                                    <span class="badge bg-primary">Entregada - Pendiente Calificación</span>
                                @endif
                                @if($entrega->estado_entrega == 'entregado_tarde')
                                    <span class="badge bg-warning text-dark ms-1">Tarde</span>
                                @endif
                            </small>
                        </li>
                    @endforeach
                </ul>
            @else
                 <div class="card-body text-center text-muted">
                    <i class="far fa-folder-open fa-2x mb-2 text-secondary"></i><br>
                    No has realizado entregas recientemente.
                </div>
            @endif
        </div>
    </div>

    <style>
        .badge.bg-info-light {
            color: #055160;
            background-color: #cff4fc;
            border: 1px solid #b6effb;
        }
    </style>
    @endsection
    