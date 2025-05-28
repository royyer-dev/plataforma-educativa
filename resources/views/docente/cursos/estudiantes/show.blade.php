@extends('layouts.app')

@section('content')
<div class="container py-4">
    {{-- Navegación y Título Principal --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('docente.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('docente.cursos.show', $curso->id) }}" class="text-decoration-none">{{ Str::limit($curso->titulo, 25) }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('docente.cursos.estudiantes.index', $curso->id) }}" class="text-decoration-none">Estudiantes</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $estudiante->nombre }} {{ $estudiante->apellidos }}</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0">Progreso del Estudiante</h1>
        </div>
        <a href="{{ route('docente.cursos.estudiantes.index', $curso->id) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Volver a Lista de Estudiantes
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
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-4">
            {{-- Información del Estudiante y Curso --}}
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center p-4">
                    <img src="{{ $estudiante->foto_url }}" alt="Foto de {{ $estudiante->nombre }}" 
                         class="rounded-circle mb-3 shadow-sm" 
                         style="width: 120px; height: 120px; object-fit: cover;">
                    <h5 class="card-title mb-1">{{ $estudiante->nombre }} {{ $estudiante->apellidos }}</h5>
                    <p class="text-muted mb-3">
                        <i class="fas fa-envelope me-1"></i>
                        {{ $estudiante->email }}
                    </p>
                    <div class="alert alert-light mb-0">
                        <strong><i class="fas fa-graduation-cap me-1"></i> Curso:</strong><br>
                        {{ $curso->titulo }}
                    </div>
                </div>
            </div>

            {{-- Sección Dar de Baja Estudiante --}}
            <div class="card border-danger shadow-sm">
                <div class="card-header bg-danger bg-opacity-10 text-danger">
                    <h5 class="mb-0"><i class="fas fa-user-minus me-2"></i>Dar de Baja del Curso</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Al dar de baja a este estudiante:
                    </p>
                    <ul class="text-muted small mb-3">
                        <li>Se eliminará su inscripción del curso</li>
                        <li>Perderá acceso al contenido del curso</li>
                        <li>No podrá realizar nuevas entregas</li>
                    </ul>
                    <form action="{{ route('docente.cursos.estudiantes.destroy', [$curso->id, $estudiante->id]) }}" 
                          method="POST" 
                          onsubmit="return confirm('¿Estás seguro de querer dar de baja a {{ $estudiante->nombre }} {{ $estudiante->apellidos }} de este curso?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="fas fa-user-slash me-1"></i> Confirmar Baja del Estudiante
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            {{-- Resumen de Calificaciones --}}
            @if($promedioGeneral !== null || !empty($promediosPorModulo) || $promedioSinModulo !== null || $tareasDelCurso->isNotEmpty())
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light d-flex align-items-center">
                    <i class="fas fa-chart-line text-primary me-2"></i>
                    <h5 class="mb-0">Resumen de Calificaciones</h5>
                </div>
                <div class="card-body">
                    @if($tareasDelCurso->whereNotNull('puntos_maximos')->where('puntos_maximos', '>', 0)->isEmpty())
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-2"></i>No hay tareas puntuables para calcular promedios.
                        </div>
                    @else
                        <div class="text-center mb-4">
                            <h6 class="text-muted mb-2">Promedio General del Curso</h6>
                            <div class="display-4 fw-bold mb-0 {{ $promedioGeneral >= 70 ? 'text-success' : ($promedioGeneral >= 50 ? 'text-warning' : 'text-danger') }}">
                                {{ $promedioGeneral !== null ? number_format($promedioGeneral, 1) . '%' : 'N/A' }}
                            </div>
                        </div>

                        @if(!empty($promediosPorModulo))
                            <hr>
                            <h6 class="fw-bold mb-3">Promedios por Módulo</h6>
                            <div class="row g-3">
                                @foreach($promediosPorModulo as $idModulo => $datosModulo)
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body p-3">
                                                <h6 class="card-title mb-2">{{ $datosModulo['titulo'] }}</h6>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="badge {{ $datosModulo['promedio'] >= 70 ? 'bg-success' : ($datosModulo['promedio'] >= 50 ? 'bg-warning' : 'bg-danger') }} p-2">
                                                        {{ $datosModulo['promedio'] !== null ? number_format($datosModulo['promedio'], 1) . '%' : 'N/A' }}
                                                    </span>
                                                    <small class="text-muted">
                                                        {{ $datosModulo['obtenidos'] }}/{{ $datosModulo['posibles'] }} pts
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if($promedioSinModulo !== null)
                            <div class="mt-3">
                                <h6 class="fw-bold mb-2">Tareas Generales (Sin Módulo)</h6>
                                <div class="card bg-light">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge {{ $promedioSinModulo >= 70 ? 'bg-success' : ($promedioSinModulo >= 50 ? 'bg-warning' : 'bg-danger') }} p-2">
                                                {{ $promedioSinModulo !== null ? number_format($promedioSinModulo, 1) . '%' : 'N/A' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
            @endif

            {{-- Resumen de Tareas y Entregas --}}
            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex align-items-center">
                    <i class="fas fa-tasks text-primary me-2"></i>
                    <h5 class="mb-0">Tareas y Entregas</h5>
                </div>
                <div class="card-body p-0">
                    @if($tareasDelCurso->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="px-3">Tarea</th>
                                        <th>Fecha Límite</th>
                                        <th class="text-center">Estado</th>
                                        <th class="text-center">Calificación</th>
                                        <th class="text-center px-3">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tareasDelCurso as $tarea)
                                        @php
                                            $entrega = $entregasEstudiante->get($tarea->id);
                                        @endphp
                                        <tr>
                                            <td class="px-3">
                                                <div class="fw-semibold">{{ $tarea->titulo }}</div>
                                                @if($tarea->modulo)
                                                    <small class="text-muted d-block">
                                                        <i class="fas fa-folder-open me-1"></i>
                                                        {{ optional($tarea->modulo)->titulo }}
                                                    </small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($tarea->fecha_limite)
                                                    <span class="text-nowrap">
                                                        <i class="far fa-calendar-alt me-1"></i>
                                                        {{ $tarea->fecha_limite->format('d/m/Y') }}
                                                    </span>
                                                    <small class="d-block text-muted">
                                                        <i class="far fa-clock me-1"></i>
                                                        {{ $tarea->fecha_limite->format('H:i') }}
                                                    </small>
                                                @else
                                                    <span class="text-muted">Sin fecha límite</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($entrega)
                                                    @if($entrega->estado_entrega == 'entregado')
                                                        <span class="badge bg-primary rounded-pill px-3 py-2">
                                                            <i class="fas fa-check me-1"></i>Entregado
                                                        </span>
                                                    @elseif($entrega->estado_entrega == 'entregado_tarde')
                                                        <span class="badge bg-warning text-dark rounded-pill px-3 py-2">
                                                            <i class="fas fa-clock me-1"></i>Tarde
                                                        </span>
                                                    @elseif($entrega->estado_entrega == 'calificado')
                                                        <span class="badge bg-success rounded-pill px-3 py-2">
                                                            <i class="fas fa-check-double me-1"></i>Calificado
                                                        </span>
                                                    @else
                                                        <span class="badge bg-secondary rounded-pill px-3 py-2">
                                                            {{ ucfirst($entrega->estado_entrega) }}
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="badge bg-light text-dark border rounded-pill px-3 py-2">
                                                        <i class="fas fa-times me-1"></i>Pendiente
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($entrega && $entrega->calificacion !== null)
                                                    <span class="fs-5 fw-bold {{ $entrega->calificacion >= ($tarea->puntos_maximos * 0.7) ? 'text-success' : ($entrega->calificacion >= ($tarea->puntos_maximos * 0.5) ? 'text-warning' : 'text-danger') }}">
                                                        {{ $entrega->calificacion }}
                                                    </span>
                                                    <small class="text-muted">/{{ $tarea->puntos_maximos ?? 'N/A' }}</small>
                                                @else
                                                    <span class="text-muted">--</span>
                                                    <small class="text-muted">/{{ $tarea->puntos_maximos ?? 'N/A' }}</small>
                                                @endif
                                            </td>
                                            <td class="text-center px-3">
                                                @if($entrega)
                                                    <a href="{{ route('docente.cursos.tareas.entregas.calificar.form', [$curso->id, $tarea->id, $entrega->id]) }}"
                                                    class="btn btn-sm {{ $entrega->calificacion !== null ? 'btn-outline-primary' : 'btn-primary' }}">
                                                        @if($entrega->calificacion !== null)
                                                            <i class="fas fa-search-plus me-1"></i>Ver/Editar
                                                        @else
                                                            <i class="fas fa-edit me-1"></i>Calificar
                                                        @endif
                                                    </a>
                                                @else
                                                    <span class="badge bg-light text-muted border">Sin entrega</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-tasks text-muted mb-2" style="font-size: 2rem;"></i>
                            <p class="text-muted mb-0">No hay tareas definidas para este curso todavía.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
