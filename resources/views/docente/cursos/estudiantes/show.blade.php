@extends('layouts.app')

@section('content')
<div class="container py-4"> {{-- Añadido padding general al container --}}

    {{-- Navegación y Título Principal --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('docente.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('docente.cursos.show', $curso->id) }}">{{ Str::limit($curso->titulo, 25) }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('docente.cursos.estudiantes.index', $curso->id) }}">Estudiantes</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $estudiante->nombre }} {{ $estudiante->apellidos }}</li>
                </ol>
            </nav>
            <h1 class="mb-0">Progreso del Estudiante</h1>
        </div>
        {{-- Podrías añadir un botón de acción principal aquí si fuera necesario --}}
    </div>

    {{-- Mensajes Flash --}}
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

    {{-- Información del Estudiante y Curso --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0 d-flex align-items-center">
                <img src="{{ $estudiante->foto_url }}" alt="Foto de {{ $estudiante->nombre }}" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                <span>{{ $estudiante->nombre }} {{ $estudiante->apellidos }}</span>
                <small class="text-muted ms-2">({{ $estudiante->email }})</small>
            </h5>
        </div>
        <div class="card-body">
            <p class="card-text mb-0"><strong>Curso:</strong> {{ $curso->titulo }}</p>
        </div>
    </div>


    {{-- Resumen de Tareas y Entregas --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fas fa-tasks me-2"></i>Resumen de Tareas y Entregas</h5>
        </div>
        <div class="card-body p-0"> {{-- p-0 para que la tabla ocupe todo el card-body --}}
            @if($tareasDelCurso->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Tarea</th>
                                <th scope="col">Fecha Límite</th>
                                <th scope="col" class="text-center">Estado Entrega</th>
                                <th scope="col" class="text-center">Calificación</th>
                                <th scope="col" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tareasDelCurso as $tarea)
                                @php
                                    $entrega = $entregasEstudiante->get($tarea->id);
                                @endphp
                                <tr>
                                    <td>
                                        <span class="fw-bold">{{ $tarea->titulo }}</span>
                                        @if($tarea->modulo)
                                            <br><small class="text-muted">Módulo: {{ optional($tarea->modulo)->titulo }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $tarea->fecha_limite ? $tarea->fecha_limite->format('d/m/Y H:i') : 'N/A' }}</td>
                                    <td class="text-center">
                                        @if($entrega)
                                            @if($entrega->estado_entrega == 'entregado') <span class="badge bg-primary rounded-pill px-2 py-1">Entregado</span>
                                            @elseif($entrega->estado_entrega == 'entregado_tarde') <span class="badge bg-warning text-dark rounded-pill px-2 py-1">Entregado Tarde</span>
                                            @elseif($entrega->estado_entrega == 'calificado') <span class="badge bg-success rounded-pill px-2 py-1">Calificado</span>
                                            @else <span class="badge bg-secondary rounded-pill px-2 py-1">{{ ucfirst($entrega->estado_entrega) }}</span>
                                            @endif
                                        @else
                                            <span class="badge bg-light text-dark border rounded-pill px-2 py-1">No Entregado</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($entrega && $entrega->calificacion !== null)
                                            <strong class="fs-5">{{ $entrega->calificacion }}</strong> / <small>{{ $tarea->puntos_maximos ?? 'N/A' }}</small>
                                        @else
                                            -- / <small>{{ $tarea->puntos_maximos ?? 'N/A' }}</small>
                                        @endif
                                    </td>
                                    <td class="text-center text-nowrap">
                                        @if($entrega)
                                            <a href="{{ route('docente.cursos.tareas.entregas.calificar.form', [$curso->id, $tarea->id, $entrega->id]) }}"
                                               class="btn btn-sm {{ $entrega->calificacion !== null ? 'btn-outline-primary' : 'btn-primary' }}">
                                               <i class="fas {{ $entrega->calificacion !== null ? 'fa-search-plus' : 'fa-edit' }} me-1"></i>
                                               {{ $entrega->calificacion !== null ? 'Ver/Editar Calificación' : 'Calificar' }}
                                            </a>
                                        @else
                                            <span class="text-muted small fst-italic">Sin entrega</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-light text-center mb-0" role="alert">
                    No hay tareas definidas para este curso todavía.
                </div>
            @endif
        </div>
    </div>


    {{-- Sección Resumen de Calificaciones --}}
    @if($promedioGeneral !== null || !empty($promediosPorModulo) || $promedioSinModulo !== null || $tareasDelCurso->isNotEmpty())
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Resumen de Calificaciones (Base 100%)</h5>
        </div>
        <div class="card-body">
            @if($tareasDelCurso->whereNotNull('puntos_maximos')->where('puntos_maximos', '>', 0)->isEmpty())
                 <p class="text-muted">No hay tareas puntuables para calcular promedios.</p>
            @else
                <dl class="row">
                    <dt class="col-sm-4 col-md-3 fs-5">Promedio General del Curso:</dt>
                    <dd class="col-sm-8 col-md-9 fs-5 fw-bold">
                        <span class="badge {{ $promedioGeneral >= 70 ? 'bg-success' : ($promedioGeneral >= 50 ? 'bg-warning text-dark' : ($promedioGeneral !== null ? 'bg-danger' : 'bg-secondary')) }} p-2">
                            {{ $promedioGeneral !== null ? number_format($promedioGeneral, 2) . '%' : 'N/A (Sin tareas calificadas)' }}
                        </span>
                    </dd>

                    @if(!empty($promediosPorModulo))
                        <hr class="my-3">
                        <dt class="col-sm-12 mb-2">Promedios por Módulo:</dt>
                         @foreach($promediosPorModulo as $idModulo => $datosModulo)
                            <dt class="col-sm-4 col-md-3 ps-md-4">{{ $datosModulo['titulo'] }}:</dt>
                            <dd class="col-sm-8 col-md-9">
                                <span class="badge {{ $datosModulo['promedio'] >= 70 ? 'bg-success' : ($datosModulo['promedio'] >= 50 ? 'bg-warning text-dark' : ($datosModulo['promedio'] !== null ? 'bg-danger' : 'bg-secondary')) }} p-2">
                                    {{ $datosModulo['promedio'] !== null ? number_format($datosModulo['promedio'], 2) . '%' : 'N/A' }}
                                </span>
                                <small class="text-muted ms-2">({{ $datosModulo['obtenidos'] }} / {{ $datosModulo['posibles'] }} pts)</small>
                            </dd>
                         @endforeach
                    @endif

                     @if($promedioSinModulo !== null || $tareasDelCurso->whereNull('modulo_id')->whereNotNull('puntos_maximos')->where('puntos_maximos', '>', 0)->isNotEmpty())
                         @if(!empty($promediosPorModulo)) <hr class="my-3"> @endif
                         <dt class="col-sm-4 col-md-3">Tareas Generales (Sin Módulo):</dt>
                         <dd class="col-sm-8 col-md-9">
                             <span class="badge {{ $promedioSinModulo >= 70 ? 'bg-success' : ($promedioSinModulo >= 50 ? 'bg-warning text-dark' : ($promedioSinModulo !== null ? 'bg-danger' : 'bg-secondary')) }} p-2">
                                {{ $promedioSinModulo !== null ? number_format($promedioSinModulo, 2) . '%' : 'N/A' }}
                             </span>
                             {{-- Podrías añadir los puntos obtenidos/posibles aquí también si los pasas desde el controller --}}
                         </dd>
                     @endif
                </dl>
            @endif
        </div>
    </div>
    @endif
    {{-- Fin Sección Resumen de Calificaciones --}}


    {{-- Sección Dar de Baja Estudiante --}}
    <hr class="my-4">
    <div class="card border-danger shadow-sm">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0"><i class="fas fa-user-minus me-2"></i>Dar de Baja del Curso</h5>
        </div>
        <div class="card-body">
             <p>Al dar de baja a este estudiante, se eliminará su inscripción del curso y ya no tendrá acceso al contenido ni podrá realizar entregas.</p>
             <form action="{{ route('docente.cursos.estudiantes.destroy', [$curso->id, $estudiante->id]) }}" method="POST" onsubmit="return confirm('¿Estás seguro de querer dar de baja a {{ $estudiante->nombre }} {{ $estudiante->apellidos }} de este curso?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">
                    <i class="fas fa-user-slash me-1"></i> Confirmar Baja del Estudiante
                </button>
            </form>
        </div>
    </div>
    {{-- Fin Sección Dar de Baja Estudiante --}}

</div> {{-- Fin container --}}
@endsection
