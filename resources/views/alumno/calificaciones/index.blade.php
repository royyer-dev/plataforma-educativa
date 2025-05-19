@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <h1><i class="fas fa-graduation-cap me-2"></i>Mis Calificaciones</h1>
        <a href="{{ route('alumno.dashboard') }}" class="btn btn-sm btn-outline-secondary mt-2 mt-md-0">
            <i class="fas fa-arrow-left me-1"></i> Volver al Dashboard
        </a>
    </div>

    {{-- Mensajes Flash --}}
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(isset($datosParaVista) && $datosParaVista->count() > 0)
        @foreach($datosParaVista as $datosCurso)
            @php
                // Extraer las variables para este curso para facilitar su uso
                $curso = $datosCurso['curso'];
                $entregasDelCurso = $datosCurso['entregas']; // Estas son las entregas calificadas para este curso
                $promediosPorModulo = $datosCurso['promediosPorModulo'];
                $promedioSinModulo = $datosCurso['promedioSinModulo']; // Promedio de tareas sin módulo para este curso
                $promedioGeneralCurso = $datosCurso['promedioGeneralCurso'];
                $puntosObtenidosCurso = $datosCurso['puntosObtenidosCurso'];
                $puntosPosiblesCurso = $datosCurso['puntosPosiblesCurso'];
            @endphp
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <a href="{{ route('alumno.cursos.show', $curso->id) }}" class="text-white text-decoration-none">
                                <i class="fas fa-book me-2"></i>{{ $curso->titulo }}
                            </a>
                        </h5>
                        <span class="badge bg-light text-primary p-2 fs-6">
                            Promedio General: {{ $promedioGeneralCurso !== null ? number_format($promedioGeneralCurso, 2) . '%' : 'N/A' }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    {{-- Promedios por Módulo --}}
                    @if($promediosPorModulo->isNotEmpty())
                        <h6 class="text-muted mt-2"><i class="fas fa-sitemap me-1"></i>Desglose por Módulo (Base 100%):</h6>
                        <ul class="list-group list-group-flush mb-3">
                            @foreach($promediosPorModulo as $datosModulo)
                                <li class="list-group-item d-flex justify-content-between align-items-center ps-0">
                                    <span>{{ $datosModulo['titulo'] }}</span>
                                    <span class="badge {{ $datosModulo['promedio'] >= 70 ? 'bg-success' : ($datosModulo['promedio'] >= 50 ? 'bg-warning text-dark' : ($datosModulo['promedio'] !== null ? 'bg-danger' : 'bg-light text-dark border')) }} rounded-pill p-2">
                                        {{ $datosModulo['promedio'] !== null ? number_format($datosModulo['promedio'], 2) . '%' : 'N/A' }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    {{-- Promedio de Tareas Sin Módulo --}}
                    @if($promedioSinModulo !== null && $curso->tareas()->whereNull('modulo_id')->whereNotNull('puntos_maximos')->where('puntos_maximos', '>', 0)->exists())
                         <h6 class="text-muted mt-2"><i class="fas fa-tasks me-1"></i>Tareas Generales (Base 100%):</h6>
                         <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item d-flex justify-content-between align-items-center ps-0">
                                <span>Promedio Tareas Generales</span>
                                <span class="badge {{ $promedioSinModulo >= 70 ? 'bg-success' : ($promedioSinModulo >= 50 ? 'bg-warning text-dark' : 'bg-danger') }} rounded-pill p-2">
                                    {{ number_format($promedioSinModulo, 2) . '%' }}
                                </span>
                            </li>
                        </ul>
                    @endif

                    {{-- Detalle de Entregas Calificadas para este Curso --}}
                    <h6 class="text-muted mt-4"><i class="fas fa-check-double me-1"></i>Detalle de Tareas Calificadas:</h6>
                    @if($entregasDelCurso->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tarea</th>
                                        <th class="text-center">Calificación</th>
                                        <th>Fecha Calificación</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($entregasDelCurso as $entrega)
                                        <tr>
                                            <td>
                                                {{ $entrega->tarea->titulo }}
                                                @if(optional($entrega->tarea->modulo)->titulo)
                                                    <br><small class="text-muted">Módulo: {{ $entrega->tarea->modulo->titulo }}</small>
                                                @endif
                                            </td>
                                            <td class="text-center fw-bold">
                                                {{ $entrega->calificacion }} / {{ $entrega->tarea->puntos_maximos ?? 'N/A' }}
                                            </td>
                                            <td>{{ $entrega->fecha_calificacion ? Carbon\Carbon::parse($entrega->fecha_calificacion)->format('d/m/Y') : '--' }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('alumno.cursos.tareas.show', [$curso->id, $entrega->tarea->id]) }}" class="btn btn-outline-primary btn-sm" title="Ver detalles de la tarea y retroalimentación">
                                                    <i class="fas fa-eye"></i> Ver
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted fst-italic">No tienes entregas calificadas para este curso aún.</p>
                    @endif
                </div>
                 <div class="card-footer text-muted small bg-light">
                    Puntos Totales Obtenidos en el Curso: <strong>{{ $puntosObtenidosCurso }}</strong> de <strong>{{ $puntosPosiblesCurso }}</strong> posibles
                </div>
            </div>
        @endforeach
    @else
        <div class="alert alert-info text-center shadow-sm p-4">
             <h4 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Sin Calificaciones</h4>
            <p>Aún no tienes calificaciones registradas en ningún curso.</p>
        </div>
    @endif
</div>
@endsection
