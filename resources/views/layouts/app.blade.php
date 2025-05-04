<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto">
                        {{-- Left Side --}}
                    </ul>
                    <ul class="navbar-nav ms-auto align-items-center">
                        @guest
                            {{-- Guest Links --}}
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            {{-- Authenticated User Links --}}
                            {{-- Role Based Links --}}
                            @if(Auth::user()->roles()->where('nombre', 'docente')->exists())
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('docente.dashboard') }}">{{ __('Dashboard Docente') }}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('docente.cursos.index') }}">{{ __('Gestionar Cursos') }}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('docente.solicitudes.index') }}">{{ __('Gestionar Solicitudes') }}</a>
                                </li>
                            @elseif(Auth::user()->roles()->where('nombre', 'estudiante')->exists())
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('alumno.dashboard') }}">{{ __('Dashboard Alumno') }}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('alumno.cursos.index') }}">{{ __('Cursos Disponibles') }}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('alumno.calificaciones.index') }}">{{ __('Mis Calificaciones') }}</a>
                                </li>
                            @endif

                            {{-- Notification Dropdown --}}
                            <li class="nav-item dropdown">
                                <a id="navbarNotificationDropdown" class="nav-link" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    <i class="fas fa-bell"></i>
                                    @if(isset($unreadNotifications) && $unreadNotifications->count() > 0)
                                        <span class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle" style="font-size: 0.6em; padding: 0.2em 0.4em;">
                                            {{ $unreadNotifications->count() }}
                                            <span class="visually-hidden">notificaciones no leídas</span>
                                        </span>
                                    @endif
                                </a>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarNotificationDropdown" style="min-width: 350px; max-height: 400px; overflow-y: auto;">
                                     <h6 class="dropdown-header">Notificaciones</h6>
                                     @if(isset($unreadNotifications))
                                         @forelse ($unreadNotifications as $notification)
                                             <a class="dropdown-item d-flex align-items-start gap-2 py-2 fw-bold"
                                                href="{{ route('notifications.read', $notification->id) }}">
                                                @if(isset($notification->data['icono']))
                                                    <i class="fas {{ $notification->data['icono'] }} fa-fw mt-1 text-{{ $notification->data['tipo'] ?? 'secondary' }}" style="width: 1.2em;"></i>
                                                @else
                                                     <i class="fas fa-info-circle fa-fw mt-1 text-secondary" style="width: 1.2em;"></i>
                                                @endif
                                                <div class="text-wrap">
                                                     <small class="d-block">{{ $notification->data['mensaje'] ?? 'Tienes una nueva notificación.' }}</small>
                                                     <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                                </div>
                                             </a>
                                             @if(!$loop->last) <div class="dropdown-divider my-1"></div> @endif
                                         @empty
                                            <span class="dropdown-item text-muted text-center small py-3">No tienes notificaciones nuevas.</span>
                                         @endforelse

                                         {{-- vvv INICIO: Botón Marcar Todas Leídas vvv --}}
                                         @if($unreadNotifications->isNotEmpty())
                                            <div class="dropdown-divider"></div>
                                            <form action="{{ route('notifications.markAllRead') }}" method="POST" class="px-3 py-1">
                                                @csrf
                                                @method('PATCH') {{-- O POST si definiste así la ruta --}}
                                                <button type="submit" class="btn btn-link btn-sm p-0 text-primary w-100 text-center">
                                                    Marcar todas como leídas
                                                </button>
                                            </form>
                                         @endif
                                         {{-- ^^^ FIN: Botón Marcar Todas Leídas ^^^ --}}

                                     @else
                                         <span class="dropdown-item text-muted text-center small py-3">Error al cargar notificaciones.</span>
                                     @endif

                                     <div class="dropdown-divider"></div>
                                     <a class="dropdown-item text-center small text-primary py-2" href="{{ route('notifications.index') }}">
                                        Ver todas las notificaciones
                                     </a>
                                </div>
                            </li>
                            {{-- Fin Notification Dropdown --}}

                            {{-- User Dropdown --}}
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->nombre }}
                                </a>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    {{-- Enlace Mi Perfil --}}
                                    <a class="dropdown-item" href="{{ route('perfil.show') }}">
                                       Mi Perfil
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    {{-- Enlace Logout --}}
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>

    {{-- Script añadido por el usuario (idealmente iría en app.js via Vite) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert-dismissible');
            alerts.forEach(alert => {
                // Asegurarse que solo se añade el listener si existe el botón de cierre
                const closeButton = alert.querySelector('.btn-close');
                if (closeButton) {
                    closeButton.addEventListener('click', function() {
                        // Usar el método dispose de Bootstrap si está disponible, si no, ocultar
                        const alertInstance = bootstrap.Alert.getOrCreateInstance(alert);
                        if (alertInstance) {
                            alertInstance.close();
                        } else {
                             alert.style.display = 'none'; // Fallback simple
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
