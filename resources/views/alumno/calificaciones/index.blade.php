@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body py-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h1 class="display-6 mb-0"><i class="fas fa-graduation-cap me-2"></i>Mis Calificaciones</h1>
                            <p class="mb-0 mt-2 opacity-75">Resumen de calificaciones por curso</p>
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
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(isset($datosParaVista) && $datosParaVista->count() > 0)
        @foreach($datosParaVista as $datosCurso)
            @php
                $curso = $datosCurso['curso'];
                $entregasDelCurso = $datosCurso['entregas'];
                $promediosPorModulo = $datosCurso['promediosPorModulo'];
                $promedioSinModulo = $datosCurso['promedioSinModulo'];
                $promedioGeneralCurso = $datosCurso['promedioGeneralCurso'];
                $puntosObtenidosCurso = $datosCurso['puntosObtenidosCurso'];
                $puntosPosiblesCurso = $datosCurso['puntosPosiblesCurso'];
            @endphp
            <div class="card shadow-sm hover-shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <a href="{{ route('alumno.cursos.show', $curso->id) }}" class="text-decoration-none text-dark">
                                <i class="fas fa-book me-2 text-primary"></i>{{ $curso->titulo }}
                            </a>
                        </h5>
                        <div class="d-flex align-items-center">
                            <div class="progress flex-grow-1 me-2" style="width: 100px; height: 8px;">
                                <div class="progress-bar {{ $promedioGeneralCurso >= 70 ? 'bg-success' : ($promedioGeneralCurso >= 50 ? 'bg-warning' : 'bg-danger') }}" 
                                     role="progressbar" 
                                     style="width: {{ $promedioGeneralCurso }}%">
                                </div>
                            </div>
                            <span class="badge bg-light text-dark border p-2">
                                Promedio: {{ $promedioGeneralCurso !== null ? number_format($promedioGeneralCurso, 2) . '%' : 'N/A' }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    {{-- Promedios por Módulo --}}
                    @if($promediosPorModulo->isNotEmpty())
                        <h6 class="text-muted mb-3">
                            <i class="fas fa-sitemap me-2"></i>Desglose por Módulo
                        </h6>
                        <div class="row g-3 mb-4">
                            @foreach($promediosPorModulo as $datosModulo)
                                <div class="col-md-6 col-lg-4">
                                    <div class="card border">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="card-title mb-0">{{ $datosModulo['titulo'] }}</h6>
                                                <span class="badge {{ $datosModulo['promedio'] >= 70 ? 'bg-success' : ($datosModulo['promedio'] >= 50 ? 'bg-warning text-dark' : ($datosModulo['promedio'] !== null ? 'bg-danger' : 'bg-light text-dark border')) }}">
                                                    {{ $datosModulo['promedio'] !== null ? number_format($datosModulo['promedio'], 2) . '%' : 'N/A' }}
                                                </span>
                                            </div>
                                            <div class="progress" style="height: 5px;">
                                                <div class="progress-bar {{ $datosModulo['promedio'] >= 70 ? 'bg-success' : ($datosModulo['promedio'] >= 50 ? 'bg-warning' : 'bg-danger') }}" 
                                                     role="progressbar" 
                                                     style="width: {{ $datosModulo['promedio'] }}%">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Promedio de Tareas Sin Módulo --}}
                    @if($promedioSinModulo !== null && $curso->tareas()->whereNull('modulo_id')->whereNotNull('puntos_maximos')->where('puntos_maximos', '>', 0)->exists())
                        <div class="card border mb-4">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0"><i class="fas fa-tasks me-2"></i>Tareas Generales</h6>
                                    <span class="badge {{ $promedioSinModulo >= 70 ? 'bg-success' : ($promedioSinModulo >= 50 ? 'bg-warning text-dark' : 'bg-danger') }}">
                                        {{ number_format($promedioSinModulo, 2) . '%' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Detalle de Entregas Calificadas --}}
                    <h6 class="text-muted mb-3">
                        <i class="fas fa-check-double me-2"></i>Detalle de Tareas Calificadas
                    </h6>
                    @if($entregasDelCurso->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tarea</th>
                                        <th class="text-center">Calificación</th>
                                        <th>Fecha</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($entregasDelCurso as $entrega)
                                        <tr>
                                            <td>
                                                <div class="fw-medium">{{ $entrega->tarea->titulo }}</div>
                                                @if(optional($entrega->tarea->modulo)->titulo)
                                                    <small class="text-muted d-block">
                                                        <i class="fas fa-folder-open me-1"></i>{{ $entrega->tarea->modulo->titulo }}
                                                    </small>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge {{ $entrega->calificacion >= ($entrega->tarea->puntos_maximos * 0.7) ? 'bg-success' : ($entrega->calificacion >= ($entrega->tarea->puntos_maximos * 0.5) ? 'bg-warning text-dark' : 'bg-danger') }} p-2">
                                                    {{ $entrega->calificacion }} / {{ $entrega->tarea->puntos_maximos ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $entrega->fecha_calificacion ? Carbon\Carbon::parse($entrega->fecha_calificacion)->format('d/m/Y') : '--' }}
                                                </small>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('alumno.cursos.tareas.show', [$curso->id, $entrega->tarea->id]) }}" 
                                                   class="btn btn-sm btn-outline-primary rounded-pill">
                                                    <i class="fas fa-eye me-1"></i>Ver
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-light text-center mb-0">
                            <i class="far fa-clipboard fa-2x text-muted mb-2"></i>
                            <p class="mb-0">No tienes entregas calificadas para este curso aún.</p>
                        </div>
                    @endif
                </div>
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between align-items-center small">
                        <span>Total de Puntos:</span>
                        <span class="fw-bold">{{ $puntosObtenidosCurso }} / {{ $puntosPosiblesCurso }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="alert alert-info shadow-sm p-4 text-center">
            <i class="fas fa-info-circle fa-3x mb-3 text-info"></i>
            <h4 class="alert-heading">Sin Calificaciones</h4>
            <p class="mb-0">Aún no tienes calificaciones registradas en ningún curso.</p>
        </div>
    @endif
</div>

<style>
.hover-shadow-sm {
    transition: all 0.2s ease-in-out;
}
.hover-shadow-sm:hover {
    transform: translateY(-2px);
    box-shadow: 0 .3rem .5rem rgba(0,0,0,.08)!important;
}
.progress {
    background-color: #f0f0f0;
}
.badge {
    font-weight: 500;
}
.table > :not(caption) > * > * {
    padding: 1rem 0.75rem;
}
</style>
@endsection
