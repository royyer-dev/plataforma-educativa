@extends('layouts.app')

@section('content')
<div class="container">
    {{-- Navegación --}}
    <div class="mb-3">
        <a href="{{ route('docente.cursos.estudiantes.index', $curso->id) }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Volver a Lista de Estudiantes
        </a>
    </div>

    {{-- Información del Estudiante y Curso --}}
    <h1>Progreso de: {{ $estudiante->nombre }} {{ $estudiante->apellidos }}</h1>
    <h4>Curso: {{ $curso->titulo }}</h4>
    <p>Correo: {{ $estudiante->email }}</p>
    <hr>

    {{-- Resumen de Entregas y Calificaciones --}}
    <h5 class="mt-4">Resumen de Tareas y Entregas</h5>

    @if($tareasDelCurso->isNotEmpty())
        <div class="table-responsive mb-4">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Tarea</th>
                        <th>Fecha Límite</th>
                        <th>Estado Entrega</th>
                        <th>Calificación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tareasDelCurso as $tarea)
                        @php
                            // Buscar la entrega de este estudiante para esta tarea
                            $entrega = $entregasEstudiante->get($tarea->id);
                        @endphp
                        <tr>
                            <td>{{ $tarea->titulo }}</td>
                            <td>{{ $tarea->fecha_limite ? $tarea->fecha_limite->format('d/m/Y H:i') : 'N/A' }}</td>
                            <td>
                                @if($entrega)
                                    {{-- Mostrar estado real de la entrega --}}
                                    @if($entrega->estado_entrega == 'entregado') <span class="badge bg-primary">Entregado</span>
                                    @elseif($entrega->estado_entrega == 'entregado_tarde') <span class="badge bg-warning text-dark">Entregado Tarde</span>
                                    @elseif($entrega->estado_entrega == 'calificado') <span class="badge bg-success">Calificado</span>
                                    @else <span class="badge bg-secondary">{{ ucfirst($entrega->estado_entrega) }}</span>
                                    @endif
                                @else
                                    <span class="badge bg-light text-dark">No Entregado</span>
                                @endif
                            </td>
                            <td>
                                {{-- Mostramos la calificación si existe --}}
                                @if($entrega && $entrega->calificacion !== null)
                                    <strong>{{ $entrega->calificacion }}</strong> / {{ $tarea->puntos_maximos ?? 'N/A' }}
                                @else
                                    -- / {{ $tarea->puntos_maximos ?? 'N/A' }}
                                @endif
                            </td>
                            <td class="text-nowrap">
                                @if($entrega)
                                    {{-- Enlace para ir a calificar/ver esa entrega específica --}}
                                    <a href="{{ route('docente.cursos.tareas.entregas.calificar.form', [$curso->id, $tarea->id, $entrega->id]) }}"
                                       class="btn btn-sm {{ $entrega->calificacion !== null ? 'btn-outline-primary' : 'btn-primary' }}">
                                       {{ $entrega->calificacion !== null ? 'Ver/Editar Calificación' : 'Calificar' }}
                                    </a>
                                @else
                                    <span class="text-muted small">Sin entrega</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Sección Resumen de Calificaciones --}}
        <div class="card mt-4 mb-4">
            <div class="card-header">
                Resumen de Calificaciones (Base 100)
            </div>
            <div class="card-body">
                <dl class="row">
                    {{-- Promedio General --}}
                    <dt class="col-sm-4 fs-5">Promedio General del Curso:</dt>
                    <dd class="col-sm-8 fs-5 fw-bold">{{ $promedioGeneral !== null ? number_format($promedioGeneral, 2) . '%' : 'N/A' }}</dd>

                    {{-- Promedios por Módulo --}}
                    @if(!empty($promediosPorModulo))
                        <hr class="my-3">
                        <dt class="col-sm-12">Promedios por Módulo:</dt>
                         @foreach($promediosPorModulo as $idModulo => $datosModulo)
                            <dt class="col-sm-4 ps-4">{{ $datosModulo['titulo'] }}:</dt>
                            <dd class="col-sm-8">
                                {{ $datosModulo['promedio'] !== null ? number_format($datosModulo['promedio'], 2) . '%' : 'N/A' }}
                                <small class="text-muted">({{ $datosModulo['obtenidos'] }} / {{ $datosModulo['posibles'] }} pts)</small>
                            </dd>
                         @endforeach
                    @endif

                     {{-- Promedio Tareas Sin Módulo --}}
                     @if($promedioSinModulo !== null || $tareasDelCurso->whereNull('modulo_id')->isNotEmpty())
                         @if(!empty($promediosPorModulo)) <hr class="my-3"> @endif
                         <dt class="col-sm-4">Tareas Generales (Sin Módulo):</dt>
                         <dd class="col-sm-8">
                             {{ $promedioSinModulo !== null ? number_format($promedioSinModulo, 2) . '%' : 'N/A' }}
                         </dd>
                     @endif
                </dl>
            </div>
        </div>
        {{-- Fin Sección Resumen de Calificaciones --}}

    @else
        <div class="alert alert-info">No hay tareas definidas para este curso todavía.</div>
    @endif

    {{-- Sección Dar de Baja Estudiante --}}
    <hr class="my-4">
    <div class="card border-danger">
        <div class="card-header bg-danger text-white">
            Dar de Baja del Curso
        </div>
        <div class="card-body">
             <p>Al dar de baja a este estudiante, se eliminará su inscripción del curso y ya no tendrá acceso al contenido ni podrá realizar entregas.</p>
             {{-- Formulario que envía la petición DELETE a la ruta destroy --}}
             <form action="{{ route('docente.cursos.estudiantes.destroy', [$curso->id, $estudiante->id]) }}" method="POST" onsubmit="return confirm('¿Estás seguro de querer dar de baja a {{ $estudiante->nombre }} {{ $estudiante->apellidos }} de este curso?')">
                @csrf
                @method('DELETE')
                {{-- vvv CORRECCIÓN: Cambiar clase del botón vvv --}}
                <button type="submit" class="btn btn-outline-danger"> {{-- Cambiado a outline --}}
                    <i class="fas fa-user-minus me-1"></i> {{-- Icono opcional --}}
                    Confirmar Baja del Estudiante
                </button>
                {{-- ^^^ FIN CORRECCIÓN ^^^ --}}
            </form>
        </div>
    </div>
     {{-- Fin Sección Dar de Baja Estudiante --}}

</div> {{-- Fin container --}}
@endsection
