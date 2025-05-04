@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2"> {{-- flex-wrap --}}
        <h1>Historial de Notificaciones</h1>
        <div>
            {{-- Botón/Formulario para Marcar Todas como Leídas --}}
            {{-- Solo mostrar si hay notificaciones no leídas --}}
            @if(Auth::user()->unreadNotifications()->count() > 0)
                <form action="{{ route('notifications.markAllRead') }}" method="POST" style="display: inline;">
                    @csrf
                    @method('PATCH') {{-- Coincide con la ruta definida --}}
                    <button type="submit" class="btn btn-sm btn-outline-secondary">Marcar todas como leídas</button>
                </form>
            @endif
        </div>
    </div>

     {{-- Mensajes Flash --}}
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($notifications && $notifications->count() > 0)
        <div class="list-group">
            @foreach ($notifications as $notification)
                <div class="list-group-item d-flex gap-3 py-3 {{ $notification->read() ? 'list-group-item-light text-muted' : 'list-group-item-primary' }}">

                    {{-- Icono --}}
                    <div class="flex-shrink-0 pt-1">
                         @if(isset($notification->data['icono']))
                            <i class="fas {{ $notification->data['icono'] }} fa-lg opacity-75 text-{{ $notification->data['tipo'] ?? 'secondary' }}" style="width: 1.5em;"></i>
                        @else
                            <i class="fas fa-info-circle fa-lg opacity-75 text-secondary" style="width: 1.5em;"></i>
                        @endif
                    </div>

                    {{-- Contenido Principal (Enlace si hay URL) --}}
                    <div class="flex-grow-1">
                         @if(isset($notification->data['url']))
                             <a href="{{ route('notifications.read', $notification->id) }}" class="text-decoration-none stretched-link {{ $notification->read() ? 'text-muted' : 'text-dark' }}">
                                <p class="mb-0">{{ $notification->data['mensaje'] ?? 'Notificación sin mensaje.' }}</p>
                             </a>
                         @else
                             {{-- Si no hay URL, solo muestra el mensaje --}}
                             <p class="mb-0 {{ $notification->read() ? '' : 'fw-bold' }}">{{ $notification->data['mensaje'] ?? 'Notificación sin mensaje.' }}</p>
                         @endif
                         <small class="opacity-75">{{ $notification->created_at->diffForHumans() }}</small>
                    </div>

                    {{-- Acciones (Indicador Nueva y Botón Eliminar) --}}
                    <div class="flex-shrink-0 d-flex flex-column align-items-end ms-2">
                         @if($notification->unread())
                            <span class="badge bg-danger rounded-pill mb-1">Nueva</span>
                         @endif
                         {{-- Botón Eliminar --}}
                         <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" onsubmit="return confirm('¿Eliminar esta notificación?');">
                            @csrf
                            @method('DELETE')
                            {{-- Usamos un botón pequeño y discreto --}}
                            <button type="submit" class="btn btn-close btn-sm p-0" aria-label="Eliminar" title="Eliminar notificación"></button>
                         </form>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Paginación --}}
        <div class="mt-4">
            {{ $notifications->links() }}
        </div>

    @else
        <div class="alert alert-info">
            No tienes notificaciones.
        </div>
    @endif

</div>
@endsection
