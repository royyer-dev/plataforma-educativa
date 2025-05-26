@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Panel del Docente</h1>
        <a href="{{ route('docente.cursos.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Crear Nuevo Curso
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
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Fila de Tarjetas de Estadísticas --}}
    <div class="row mb-4">
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card text-white bg-primary shadow h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fs-4 fw-bold">{{ $totalCursosActivos ?? 0 }}</div>
                            <div>Cursos Activos</div>
                        </div>
                        <i class="fas fa-chalkboard-teacher fa-2x opacity-50"></i>
                    </div>
                </div>
                <a href="{{ route('docente.cursos.index') }}" class="card-footer text-white clearfix small z-1 text-decoration-none">
                    <span class="float-start">Ver Detalles</span>
                    <span class="float-end"><i class="fas fa-angle-right"></i></span>
                </a>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card text-white bg-success shadow h-100">
                <div class="card-body">
                     <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fs-4 fw-bold">{{ $totalEstudiantesInscritos ?? 0 }}</div>
                            <div>Estudiantes Totales</div>
                        </div>
                        <i class="fas fa-users fa-2x opacity-50"></i>
                    </div>
                </div>
                <a href="{{ route('docente.estudiantes.generales') }}" class="card-footer text-white clearfix small z-1 text-decoration-none" title="Ver todos mis estudiantes">
                    <span class="float-start">Ver Detalles</span>
                    <span class="float-end"><i class="fas fa-angle-right"></i></span>
                </a>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card text-white bg-warning shadow h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fs-4 fw-bold">{{ $solicitudesPendientesCount ?? 0 }}</div>
                            <div>Solicitudes Pendientes</div>
                        </div>
                        <i class="fas fa-user-plus fa-2x opacity-50"></i>
                    </div>
                </div>
                <a href="{{ route('docente.solicitudes.index') }}" class="card-footer text-white clearfix small z-1 text-decoration-none">
                    <span class="float-start">Gestionar</span>
                    <span class="float-end"><i class="fas fa-angle-right"></i></span>
                </a>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card text-white bg-danger shadow h-100">
                <div class="card-body">
                     <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fs-4 fw-bold">{{ $totalTareasSinCalificar ?? 0 }}</div>
                            <div>Entregas por Calificar</div>
                        </div>
                        <i class="fas fa-edit fa-2x opacity-50"></i>
                    </div>
                </div>
                <a href="{{ route('docente.entregas.porCalificar') }}" class="card-footer text-white clearfix small z-1 text-decoration-none" title="Ver todas las entregas pendientes">
                    <span class="float-start">Ver Detalles</span>
                    <span class="float-end"><i class="fas fa-angle-right"></i></span>
                </a>
            </div>
        </div>
    </div>

    {{-- Fila para Cursos Recientes y Actividad Reciente --}}
    <div class="row">
        <div class="col-lg-7 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-list-alt me-1"></i> Mis Cursos (Últimos 5 o Activos)</span>
                    <a href="{{ route('docente.cursos.index') }}" class="btn btn-sm btn-outline-secondary">Ver Todos</a>
                </div>
                <div class="card-body">
                    @if($cursosDocente && $cursosDocente->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($cursosDocente->take(5) as $curso)
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
                                    <div>
                                        <a href="{{ route('docente.cursos.show', $curso->id) }}" class="text-decoration-none fw-bold">{{ $curso->titulo }}</a>
                                        <br>
                                        <small class="text-muted">Estado: {{ ucfirst($curso->estado) }} | Estudiantes Activos: {{ $curso->estudiantes_activos_count }}</small>
                                    </div>
                                    <a href="{{ route('docente.cursos.show', $curso->id) }}" class="btn btn-sm btn-outline-primary">Gestionar</a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">Aún no has creado ningún curso.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-5 mb-4">
            <div class="card h-100 shadow-sm">
                 <div class="card-header bg-light">
                    <i class="fas fa-history me-1"></i> Actividad Reciente (Últimas Entregas)
                </div>
                <div class="card-body">
                    @if($ultimasEntregas && $ultimasEntregas->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($ultimasEntregas as $entrega)
                                <li class="list-group-item px-0 py-3">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">
                                            <a href="{{ route('docente.cursos.tareas.entregas.calificar.form', [optional($entrega->tarea->curso)->id, optional($entrega->tarea)->id, $entrega->id]) }}" class="text-decoration-none">
                                                {{ Str::limit(optional($entrega->tarea)->titulo, 35) }}
                                            </a>
                                        </h6>
                                        <small class="text-muted">{{ $entrega->created_at->diffForHumans() }}</small>
                                    </div>
                                    <small class="text-muted">
                                        Estudiante: {{ optional($entrega->estudiante)->nombre }} {{ optional($entrega->estudiante)->apellidos }}<br>
                                        Curso: {{ Str::limit(optional(optional($entrega->tarea)->curso)->titulo, 30) }}
                                    </small>
                                </li>
                            @endforeach
                        </ul>
                    @else
                         <p class="text-muted">No hay entregas recientes.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Sección de Gráficas --}}
    <div class="row mt-2">
        {{-- Gráfica Estudiantes por Curso --}}
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-users-cog me-2 text-primary"></i>Distribución de Estudiantes por Curso</h5>
                </div>
                <div class="card-body p-2" style="min-height: 300px; position: relative;"> {{-- position: relative para el canvas --}}
                    {{-- Solo mostrar la gráfica si hay datos para ella --}}
                    @if(isset($chartEstudiantesPorCursoData) && collect($chartEstudiantesPorCursoData)->sum() > 0)
                        <canvas id="estudiantesPorCursoChart"></canvas>
                    @else
                        <div class="d-flex align-items-center justify-content-center h-100">
                             <p class="text-center text-muted p-5">No hay datos de estudiantes para mostrar en la gráfica.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Gráfica Estado de Entregas --}}
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                     <h5 class="mb-0"><i class="fas fa-tasks me-2 text-info"></i>Estado General de Entregas</h5>
                </div>
                <div class="card-body p-2" style="min-height: 300px; position: relative;"> {{-- position: relative para el canvas --}}
                     {{-- Solo mostrar la gráfica si hay datos para ella (al menos una entrega total) --}}
                     @if(isset($chartEstadoEntregasData) && (isset($chartEstadoEntregasData[0]) || isset($chartEstadoEntregasData[1])) && ( ($chartEstadoEntregasData[0] ?? 0) > 0 || ($chartEstadoEntregasData[1] ?? 0) > 0) )
                        <canvas id="estadoEntregasChart"></canvas>
                    @else
                        <div class="d-flex align-items-center justify-content-center h-100">
                            <p class="text-center text-muted p-5">No hay datos de entregas para mostrar en la gráfica.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
{{-- Script para Chart.js --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Gráfica de Estudiantes por Curso (Doughnut)
    const ctxEstudiantes = document.getElementById('estudiantesPorCursoChart');
    const estudiantesLabels = @json($chartEstudiantesPorCursoLabels ?? []);
    const estudiantesData = @json($chartEstudiantesPorCursoData ?? []);

    if (ctxEstudiantes && estudiantesData.length > 0 && estudiantesData.some(d => d > 0)) {
        new Chart(ctxEstudiantes, {
            type: 'doughnut',
            data: {
                labels: estudiantesLabels,
                datasets: [{
                    label: 'Nº de Estudiantes',
                    data: estudiantesData,
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.8)', 'rgba(255, 99, 132, 0.8)',
                        'rgba(255, 206, 86, 0.8)', 'rgba(75, 192, 192, 0.8)',
                        'rgba(153, 102, 255, 0.8)', 'rgba(255, 159, 64, 0.8)',
                        'rgba(46, 204, 113, 0.8)', 'rgba(231, 76, 60, 0.8)',
                        'rgba(241, 196, 15, 0.8)', 'rgba(52, 73, 94, 0.8)'
                        // Añade más colores si tienes muchos cursos
                    ],
                    borderColor: '#fff',
                    borderWidth: 2,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            boxWidth: 12
                        }
                    },
                    title: {
                        display: false,
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed !== null) {
                                    label += context.parsed + ' estudiante(s)';
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    }

    // 2. Gráfica de Estado de Entregas (Barras Horizontales)
    const ctxEntregas = document.getElementById('estadoEntregasChart');
    const entregasLabels = @json($chartEstadoEntregasLabels ?? []);
    const entregasData = @json($chartEstadoEntregasData ?? []);

    if (ctxEntregas && entregasData.length > 0 && entregasData.some(d => d > 0)) {
        new Chart(ctxEntregas, {
            type: 'bar',
            data: {
                labels: entregasLabels,
                datasets: [{
                    label: 'Número de Entregas',
                    data: entregasData,
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.8)', // Calificadas
                        'rgba(255, 99, 132, 0.8)'  // Pendientes
                    ],
                    borderColor: [
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 99, 132, 1)'
                    ],
                    borderWidth: 1,
                    barPercentage: 0.6, // Hacer barras un poco más delgadas
                    categoryPercentage: 0.7
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: Math.max(1, Math.ceil(Math.max(...entregasData) / 5)), // Ajustar stepSize dinámicamente
                            precision: 0 // Asegurar números enteros
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: false,
                    }
                }
            }
        });
    }
});
</script>
@endpush
