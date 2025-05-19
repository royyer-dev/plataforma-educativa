<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'MiTec') }}</title>
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" xintegrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <style>
        .navbar-brand-logo {
            height: 30px;
            width: auto;
            margin-right: 8px;
            vertical-align: middle;
        }
        .navbar-profile-img {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 8px;
            border: 1px solid #ddd;
        }
        .navbar-brand {
            display: inline-flex;
            align-items: center;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <img src="{{ asset('images/logo_mitec.png') }}" alt="Logo" class="navbar-brand-logo">
                    <span>{{ config('app.name', 'MiTec') }}</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto">
                    </ul>
                    <ul class="navbar-nav ms-auto align-items-center">
                        @guest
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
                            @if(Auth::user()->roles()->where('nombre', 'docente')->exists())
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('docente.dashboard') }}">{{ __('Dashboard') }}</a>
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
                                    <a class="nav-link" href="{{ route('alumno.carreras.index') }}">{{ __('Carreras y Cursos') }}</a>
                                </li>
                                <li class="nav-item">
                                        <a class="nav-link" href="{{ route('alumno.agenda.index') }}"><i class="fas fa-calendar-alt me-1"></i>Mi Agenda</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('alumno.calificaciones.index') }}"><i class="fas fa-graduation-cap me-1"></i>Mis Calificaciones</a>
                                </li>
                            @endif

                            {{-- Desplegable de Notificaciones --}}
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
                                             <a class="dropdown-item d-flex align-items-start gap-2 py-2 {{ $notification->read() ? '' : 'fw-bold' }}"
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
                                         @if(isset($unreadNotifications) && $unreadNotifications->isNotEmpty())
                                            <div class="dropdown-divider"></div>
                                            <form action="{{ route('notifications.markAllRead') }}" method="POST" class="px-3 py-1">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-link btn-sm p-0 text-primary w-100 text-center">
                                                    Marcar todas como leídas
                                                </button>
                                            </form>
                                         @endif
                                     @else
                                         <span class="dropdown-item text-muted text-center small py-3">Error al cargar notificaciones.</span>
                                     @endif
                                     <div class="dropdown-divider"></div>
                                     <a class="dropdown-item text-center small text-primary py-2" href="{{ route('notifications.index') }}">
                                        Ver todas las notificaciones
                                     </a>
                                </div>
                            </li>
                            {{-- Fin Desplegable de Notificaciones --}}

                            {{-- Menú Desplegable del Usuario --}}
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    <img src="{{ Auth::user()->foto_url }}" alt="Foto de perfil" class="navbar-profile-img">
                                    {{ Auth::user()->nombre }}
                                </a>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('perfil.show') }}">
                                       <i class="fas fa-user-circle me-2"></i>Mi Perfil
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i>{{ __('Logout') }}
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
    {{-- Script para alertas (se mantiene) --}}
    <script>
        // Tu script para cerrar alertas
    </script>
</body>
</html>
