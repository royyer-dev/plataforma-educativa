@extends('layouts.app')

@section('content')
<div class="container py-4">
    {{-- Cabecera --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none">Inicio</a></li>
                    <li class="breadcrumb-item active">Notificaciones</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0">
                <i class="fas fa-bell text-primary me-2"></i>Historial de Notificaciones
            </h1>
        </div>
        @if(Auth::user()->unreadNotifications()->count() > 0)
            <form action="{{ route('notifications.markAllRead') }}" method="POST">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-outline-primary">
                    <i class="fas fa-check-double me-2"></i>Marcar todas como leídas
                </button>
            </form>
        @endif
    </div>

    {{-- Mensajes Flash --}}
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Lista de Notificaciones --}}
    @if ($notifications && $notifications->count() > 0)
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @foreach ($notifications as $notification)
                        <div class="list-group-item border-start-0 border-end-0 {{ $notification->read() ? 'bg-light' : 'bg-white' }}">
                            <div class="d-flex gap-3 py-2">
                                {{-- Icono --}}
                                <div class="flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                    <div class="rounded-circle p-2 {{ $notification->read() ? 'bg-secondary bg-opacity-10' : 'bg-primary bg-opacity-10' }}">
                                        @if(isset($notification->data['icono']))
                                            <i class="fas {{ $notification->data['icono'] }} fa-lg text-{{ $notification->data['tipo'] ?? 'secondary' }}"></i>
                                        @else
                                            <i class="fas fa-info-circle fa-lg text-secondary"></i>
                                        @endif
                                    </div>
                                </div>

                                {{-- Contenido Principal --}}
                                <div class="flex-grow-1">
                                    @if(isset($notification->data['url']))
                                        <a href="{{ route('notifications.read', $notification->id) }}" 
                                           class="text-decoration-none stretched-link">
                                            <p class="mb-1 {{ $notification->read() ? 'text-muted' : 'text-dark fw-semibold' }}">
                                                {{ $notification->data['mensaje'] ?? 'Notificación sin mensaje.' }}
                                            </p>
                                        </a>
                                    @else
                                        <p class="mb-1 {{ $notification->read() ? 'text-muted' : 'text-dark fw-semibold' }}">
                                            {{ $notification->data['mensaje'] ?? 'Notificación sin mensaje.' }}
                                        </p>
                                    @endif
                                    <div class="d-flex align-items-center gap-2">
                                        <small class="text-muted">
                                            <i class="far fa-clock me-1"></i>{{ $notification->created_at->diffForHumans() }}
                                        </small>
                                        @if($notification->unread())
                                            <span class="badge bg-primary rounded-pill">Nueva</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Acciones --}}
                                <div class="flex-shrink-0">
                                    <form action="{{ route('notifications.destroy', $notification->id) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta notificación?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-link text-danger p-0" title="Eliminar notificación">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Paginación --}}
        <div class="mt-4 d-flex justify-content-center">
            {{ $notifications->links() }}
        </div>
    @else
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-bell-slash text-muted mb-3" style="font-size: 3rem;"></i>
                <h5 class="text-muted mb-0">No tienes notificaciones</h5>
            </div>
        </div>
    @endif
</div>
@endsection
