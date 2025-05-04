@extends('layouts.app') {{-- Usa tu layout principal --}}

@section('content')
<div class="container">
    <div class="row mb-3">
        <div class="col-md-6">
            <h1>Mis Cursos</h1>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('docente.cursos.create') }}" class="btn btn-primary">Crear Nuevo Curso</a>
        </div>
    </div>

    {{-- Mensaje de status --}}
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($cursos->isEmpty())
        <div class="alert alert-info">
            Aún no has creado ningún curso.
        </div>
    @else
        <div class="list-group">
            @foreach ($cursos as $curso)
                {{-- Usamos list-group-item para cada curso --}}
                <div class="list-group-item list-group-item-action flex-column align-items-start mb-2">
                    {{-- Título y Código --}}
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1">{{ $curso->titulo }}</h5>
                        <small>Código: {{ $curso->codigo_curso ?? 'N/A' }}</small>
                    </div>
                    {{-- Descripción y Detalles --}}
                    <p class="mb-1">{{ Str::limit($curso->descripcion, 150) }}</p>
                    <small>Categoría: {{ optional($curso->categoria)->nombre ?? 'Sin categoría' }} | Estado: {{ ucfirst($curso->estado) }}</small>

                    {{-- vvv Contenedor de Botones Modificado vvv --}}
                    {{-- Usamos flexbox para separar los grupos de botones --}}
                    <div class="mt-3 d-flex justify-content-between align-items-center">
                        {{-- Grupo de botones principales (izquierda) --}}
                        <div class="btn-group btn-group-sm" role="group" aria-label="Acciones principales">
                            <a href="{{ route('docente.cursos.show', $curso->id) }}" class="btn btn-info">Ver Contenido</a>
                            <a href="{{ route('docente.cursos.edit', $curso->id) }}" class="btn btn-warning">Editar Curso</a>
                            <a href="{{ route('docente.cursos.estudiantes.index', $curso->id) }}" class="btn btn-success">Gestionar Estudiantes</a>
                        </div>

                        {{-- Botón Eliminar (derecha) --}}
                        <div>
                            <form action="{{ route('docente.cursos.destroy', $curso->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('¿Estás seguro de querer eliminar este curso? Esta acción no se puede deshacer.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"> {{-- Usamos outline para menos énfasis --}}
                                    <i class="fas fa-trash-alt me-1"></i> {{-- Icono opcional --}}
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                    {{-- ^^^ Fin Contenedor de Botones Modificado ^^^ --}}

                </div>
            @endforeach
        </div>

         {{-- Paginación --}}
        <div class="mt-4">
            {{ $cursos->links() }}
        </div>
    @endif
</div>
@endsection
