@extends('layouts.app')

@push('styles')
<style>
    /* Estilos para la tabla de cursos */
    .table {
        margin-bottom: 0;
    }
    .table th {
        background-color: #f8f9fa;
        border-top: none;
        font-weight: 600;
        color: #2c3e50;
    }
    .table td {
        vertical-align: middle;
    }
    
    /* Títulos de los cursos */
    .course-title {
        color: #2c3e50;
        text-decoration: none;
        font-weight: 600;
        transition: color 0.2s ease;
    }
    .course-title:hover {
        color: #0d6efd;
    }
    
    /* Descripción de los cursos */
    .course-description {
        color: #6c757d;
        font-size: 0.875rem;
        margin: 0;
    }
    
    /* Botones de acción */
    .btn-group .btn {
        padding: 0.375rem 0.75rem;
        transition: all 0.2s ease;
    }
    .btn-group .btn:hover {
        transform: translateY(-1px);
    }
    .btn-group .btn i {
        font-size: 0.875rem;
    }
    
    /* Badges de estado */
    .badge {
        padding: 0.5em 0.8em;
        font-weight: 500;
    }
    .badge.bg-success {
        background-color: #28a745 !important;
    }
    .badge.bg-warning {
        background-color: #ffc107 !important;
    }
    
    /* Hover effects */
    .table tbody tr {
        transition: background-color 0.2s ease;
    }
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    /* Card container */
    .card {
        border: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }
    .card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    /* Contador de estudiantes */
    .student-count {
        font-weight: 600;
        color: #2c3e50;
    }
    
    /* Botones principales */
    .btn-primary {
        padding: 0.5rem 1.5rem;
        font-weight: 500;
        box-shadow: 0 2px 4px rgba(13, 110, 253, 0.2);
        transition: all 0.2s ease;
    }
    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(13, 110, 253, 0.3);
    }
    
    /* Estado vacío */
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
    }
    .empty-state h4 {
        color: #2c3e50;
        margin-bottom: 1rem;
    }
    .empty-state p {
        color: #6c757d;
        margin-bottom: 2rem;
    }
    
    /* Paginación */
    .pagination {
        margin: 0;
        padding: 1rem;
    }
    .page-link {
        border: none;
        padding: 0.5rem 1rem;
        color: #2c3e50;
        transition: all 0.2s ease;
    }
    .page-link:hover {
        background-color: #e9ecef;
        transform: translateY(-1px);
    }
    .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
</style>
@endpush

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
                                        <a href="{{ route('docente.cursos.show', $curso->id) }}" class="fw-bold text-decoration-none course-title">{{ $curso->titulo }}</a>
                                        <p class="small text-muted mb-0 course-description">{{ Str::limit($curso->descripcion, 70) }}</p>
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
                                        <span class="student-count">{{ $curso->estudiantes()->wherePivot('estado', 'activo')->count() }}</span>
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
