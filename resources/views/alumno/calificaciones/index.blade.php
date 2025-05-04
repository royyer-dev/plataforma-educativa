@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Mis Calificaciones</h1>
    <hr>

    @if($calificacionesPorCurso && $calificacionesPorCurso->count() > 0)

        {{-- Iterar sobre cada curso que tiene calificaciones --}}
        @foreach($calificacionesPorCurso as $cursoId => $entregasDelCurso)
            {{-- Obtener el nombre del curso (asumiendo que todas las entregas tienen la misma tarea->curso) --}}
            @php
                $curso = $entregasDelCurso->first()->tarea->curso; // Obtener el objeto Curso de la primera entrega
            @endphp

            <div class="card mb-4">
                <div class="card-header">
                    {{-- Enlace opcional al curso --}}
                    <a href="{{ route('alumno.cursos.show', $curso->id) }}">
                       <strong>Curso: {{ $curso->titulo }}</strong>
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Tarea</th>
                                <th>Calificación</th>
                                <th>Puntos Máximos</th>
                                <th>Fecha Calificación</th>
                                <th>Retroalimentación</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Iterar sobre las entregas calificadas de este curso --}}
                            @foreach($entregasDelCurso as $entrega)
                                <tr>
                                    {{-- Enlace opcional a la tarea --}}
                                    <td>
                                        <a href="{{ route('alumno.cursos.tareas.show', [$curso->id, $entrega->tarea->id]) }}">
                                            {{ $entrega->tarea->titulo }}
                                        </a>
                                    </td>
                                    <td><strong>{{ $entrega->calificacion }}</strong></td>
                                    <td>{{ $entrega->tarea->puntos_maximos ?? 'N/A' }}</td>
                                    <td>{{ $entrega->fecha_calificacion ? $entrega->fecha_calificacion->format('d/m/Y') : '--' }}</td>
                                    <td>
                                        @if($entrega->retroalimentacion)
                                            {{-- Botón opcional para ver retroalimentación en modal si es larga --}}
                                            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#retroModal{{ $entrega->id }}">
                                              Ver
                                            </button>
                                            <div class="modal fade" id="retroModal{{ $entrega->id }}" tabindex="-1" aria-hidden="true">
                                              <div class="modal-dialog modal-dialog-scrollable">
                                                <div class="modal-content">
                                                  <div class="modal-header">
                                                    <h5 class="modal-title">Retroalimentación para: {{ $entrega->tarea->titulo }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                  </div>
                                                  <div class="modal-body">
                                                    {!! nl2br(e($entrega->retroalimentacion)) !!}
                                                  </div>
                                                </div>
                                              </div>
                                            </div>
                                        @else
                                            --
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div> {{-- Fin card --}}
        @endforeach

    @else
        <div class="alert alert-info">
            Aún no tienes calificaciones registradas.
        </div>
    @endif
</div>
@endsection
