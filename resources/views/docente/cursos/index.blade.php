@extends('layouts.app') {{-- Usa tu layout principal --}}

@section('content')
<div class="container py-4">

    {{-- Breadcrumbs para navegación --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('docente.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Mis Cursos</li>
        </ol>
    </nav>

    {{-- Encabezado de la página y botón de acción principal --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-chalkboard-teacher me-2"></i>Mis Cursos</h1>
        <a href="{{ route('docente.cursos.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Crear Nuevo Curso
        </a>
    </div>

    {{-- Mensaje de status (éxito/error) --}}
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

    {{-- Contenedor de la tabla con sombra y padding --}}
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0">Listado de Cursos Impartidos</h5>
        </div>
        <div class="card-body p-0"> {{-- p-0 para que la tabla ocupe todo el card-body --}}
            @if($cursos->isEmpty())
                <div class="alert alert-info mb-0 text-center"> {{-- mb-0 si es el único elemento --}}
                    <h4 class="alert-heading mt-3">¡Aún no hay cursos!</h4>
                    <p>Parece que todavía no has creado ningún curso. ¡Empieza ahora mismo!</p>
                    <hr>
                    <a href="{{ route('docente.cursos.create') }}" class="btn btn-lg btn-primary mb-3">
                        <i class="fas fa-plus me-1"></i> Crear Mi Primer Curso
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0"> {{-- table-hover y quitamos margen inferior --}}
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Título del Curso</th>
                                <th scope="col">Código</th>
                                <th scope="col">Carrera</th>
                                <th scope="col" class="text-center">Estado</th>
                                <th scope="col" class="text-center">Estudiantes</th>
                                <th scope="col" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cursos as $curso)
                                <tr>
                                    <td>
                                        <a href="{{ route('docente.cursos.show', $curso->id) }}" class="fw-bold text-decoration-none">{{ $curso->titulo }}</a>
                                        <p class="small text-muted mb-0">{{ Str::limit($curso->descripcion, 70) }}</p>
                                    </td>
                                    <td>{{ $curso->codigo_curso ?? 'N/A' }}</td>
                                    <td>{{ optional($curso->carrera)->nombre ?? 'Sin carrera' }}</td>
                                    <td class="text-center">
                                        @if($curso->estado == 'publicado')
                                            <span class="badge bg-success rounded-pill">{{ ucfirst($curso->estado) }}</span>
                                        @elseif($curso->estado == 'borrador')
                                            <span class="badge bg-warning text-dark rounded-pill">{{ ucfirst($curso->estado) }}</span>
                                        @else
                                            <span class="badge bg-secondary rounded-pill">{{ ucfirst($curso->estado) }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        {{-- Contar estudiantes activos para este curso específico --}}
                                        {{ $curso->estudiantes()->wherePivot('estado', 'activo')->count() }}
                                    </td>
                                    <td class="text-center text-nowrap">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('docente.cursos.show', $curso->id) }}" class="btn btn-outline-info" title="Ver Contenido"><i class="fas fa-eye"></i></a>
                                            <a href="{{ route('docente.cursos.edit', $curso->id) }}" class="btn btn-outline-warning" title="Editar Curso"><i class="fas fa-pen"></i></a>
                                            <a href="{{ route('docente.cursos.estudiantes.index', $curso->id) }}" class="btn btn-outline-success" title="Gestionar Estudiantes"><i class="fas fa-users"></i></a>
                                            <form action="{{ route('docente.cursos.destroy', $curso->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('¿Estás seguro de querer eliminar este curso? Esta acción no se puede deshacer y borrará todo su contenido asociado.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Eliminar Curso">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div> {{-- Fin card-body --}}
        @if(!$cursos->isEmpty() && $cursos->hasPages())
            <div class="card-footer bg-light">
                {{ $cursos->links() }} {{-- Paginación --}}
            </div>
        @endif
    </div> {{-- Fin card --}}
</div>
@endsection
