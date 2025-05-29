<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'MiTec') }}</title>
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <style>
        :root {
            --primary-color: #0d6efd;
            --navbar-height: 64px;
        }

        body {
            min-height: 100vh;
            background-color: #f8f9fa;
        }

        .navbar {
            height: var(--navbar-height);
            background: white !important;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }

        .navbar-brand-logo {
            height: 36px;
            width: auto;
            margin-right: 12px;
            vertical-align: middle;
            transition: transform 0.2s ease;
        }

        .navbar-brand:hover .navbar-brand-logo {
            transform: scale(1.05);
        }

        .navbar-brand {
            display: inline-flex;
            align-items: center;
            font-weight: 600;
            font-size: 1.25rem;
            color: var(--primary-color) !important;
            transition: opacity 0.2s ease;
        }

        .navbar-brand:hover {
            opacity: 0.85;
        }

        .navbar-profile-img {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
            border: 2px solid #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.2s ease;
        }

        .nav-link {
            padding: 0.5rem 1rem;
            font-weight: 500;
            color: #495057;
            position: relative;
            transition: color 0.2s ease;
        }

        .nav-link:hover {
            color: var(--primary-color);
        }

        .nav-link i {
            margin-right: 6px;
            font-size: 0.9em;
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border-radius: 0.5rem;
            padding: 0.5rem;
        }

        .dropdown-item {
            padding: 0.6rem 1rem;
            border-radius: 0.3rem;
            transition: all 0.2s ease;
        }

        .dropdown-item:hover {
            background-color: rgba(13, 110, 253, 0.1);
            color: var(--primary-color);
        }

        .dropdown-item i {
            width: 1.2em;
            text-align: center;
        }

        .dropdown-divider {
            margin: 0.5rem 0;
            opacity: 0.1;
        }

        /* Estilo para notificaciones */
        #navbarNotificationDropdown {
            position: relative;
            padding: 0.5rem;
            font-size: 1.2rem;
            color: #6c757d;
            transition: color 0.2s ease;
        }

        #navbarNotificationDropdown:hover {
            color: var(--primary-color);
        }

        .notification-badge {
            position: absolute;
            top: 0;
            right: 0;
            transform: translate(25%, -25%);
            padding: 0.25rem 0.4rem;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .notification-item {
            transition: background-color 0.2s ease;
        }

        .notification-item:hover {
            background-color: rgba(13, 110, 253, 0.05);
        }

        .notification-icon {
            font-size: 1rem;
            width: 1.5rem;
            text-align: center;
        }

        /* Estilos para el contenido principal */
        main {
            min-height: calc(100vh - var(--navbar-height));
            padding: 2rem 0;
        }

        /* Estilos para las alertas */
        .alert {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .alert-success {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #842029;
        }

        /* Estilos para botones en la navbar */
        .navbar .btn {
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        /* Animaciones suaves para interacciones */
        .fade-enter-active, .fade-leave-active {
            transition: opacity 0.2s ease;
        }

        .fade-enter, .fade-leave-to {
            opacity: 0;
        }
    </style>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light sticky-top">
            <div class="container">
                {{-- Logo y Nombre --}}
                <a class="navbar-brand" href="{{ url('/') }}">
                    <img src="{{ asset('images/logo_mitec.png') }}" alt="Logo" class="navbar-brand-logo">
                    <span>{{ config('app.name', 'MiTec') }}</span>
                </a>

                {{-- Botón para móviles --}}
                <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto">
                    </ul>

                    {{-- Menú de navegación --}}
                    <ul class="navbar-nav ms-auto align-items-center gap-1">
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="btn btn-outline-primary" href="{{ route('login') }}">
                                        <i class="fas fa-sign-in-alt me-1"></i>{{ __('Iniciar Sesión') }}
                                    </a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item ms-2">
                                    <a class="btn btn-primary" href="{{ route('register') }}">
                                        <i class="fas fa-user-plus me-1"></i>{{ __('Registrarse') }}
                                    </a>
                                </li>
                            @endif
                        @else
                            @if(Auth::user()->roles()->where('nombre', 'docente')->exists())
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('docente.dashboard') }}">
                                        <i class="fas fa-columns"></i>{{ __('Dashboard') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('docente.cursos.index') }}">
                                        <i class="fas fa-chalkboard-teacher"></i>{{ __('Cursos') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('docente.solicitudes.index') }}">
                                        <i class="fas fa-tasks"></i>{{ __('Solicitudes') }}
                                    </a>
                                </li>
                            @elseif(Auth::user()->roles()->where('nombre', 'estudiante')->exists())
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('alumno.dashboard') }}">
                                        <i class="fas fa-columns"></i>{{ __('Dashboard') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('alumno.carreras.index') }}">
                                        <i class="fas fa-graduation-cap"></i>{{ __('Carreras') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('alumno.agenda.index') }}">
                                        <i class="fas fa-calendar-alt"></i>Agenda
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('alumno.calificaciones.index') }}">
                                        <i class="fas fa-star"></i>Calificaciones
                                    </a>
                                </li>
                            @endif

                            {{-- Notificaciones --}}
                            <li class="nav-item dropdown">
                                <a id="navbarNotificationDropdown" class="nav-link" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-bell"></i>
                                    @if(isset($unreadNotifications) && $unreadNotifications->count() > 0)
                                        <span class="badge bg-danger notification-badge">
                                            {{ $unreadNotifications->count() }}
                                        </span>
                                    @endif
                                </a>

                                <div class="dropdown-menu dropdown-menu-end shadow-sm" style="min-width: 350px; max-height: 400px; overflow-y: auto;">
                                    <h6 class="dropdown-header fw-bold">Notificaciones</h6>
                                    @if(isset($unreadNotifications))
                                        @forelse ($unreadNotifications as $notification)
                                            <a class="dropdown-item notification-item d-flex align-items-center gap-2 py-2 px-3 {{ $notification->read() ? '' : 'fw-bold' }}"
                                               href="{{ route('notifications.read', $notification->id) }}">
                                                <i class="notification-icon fas {{ $notification->data['icono'] ?? 'fa-info-circle' }} text-{{ $notification->data['tipo'] ?? 'secondary' }}"></i>
                                                <div class="flex-grow-1">
                                                    <div class="mb-1">{{ $notification->data['mensaje'] ?? 'Nueva notificación.' }}</div>
                                                    <div class="text-muted small">{{ $notification->created_at->diffForHumans() }}</div>
                                                </div>
                                            </a>
                                            @if(!$loop->last)
                                                <div class="dropdown-divider"></div>
                                            @endif
                                        @empty
                                            <div class="dropdown-item text-center text-muted py-3">
                                                <i class="fas fa-check-circle mb-2"></i>
                                                <p class="mb-0">No hay notificaciones nuevas</p>
                                            </div>
                                        @endforelse

                                        @if($unreadNotifications->isNotEmpty())
                                            <div class="dropdown-divider"></div>
                                            <form action="{{ route('notifications.markAllRead') }}" method="POST" class="px-3 py-2">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-link btn-sm text-primary w-100">
                                                    <i class="fas fa-check-double me-1"></i>Marcar todas como leídas
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item text-center text-primary py-2" href="{{ route('notifications.index') }}">
                                        Ver todas las notificaciones
                                        <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </li>

                            {{-- Perfil de Usuario --}}
                            <li class="nav-item dropdown ms-3">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <img src="{{ Auth::user()->foto_url }}" alt="Foto de perfil" class="navbar-profile-img">
                                    <span class="d-none d-md-inline">{{ Auth::user()->nombre }}</span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-end shadow-sm">
                                    <a class="dropdown-item" href="{{ route('perfil.show') }}">
                                        <i class="fas fa-user-circle"></i>Mi Perfil
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt"></i>{{ __('Cerrar Sesión') }}
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

        <main>
            @yield('content')
        </main>
    </div>

    <script>
        // Cerrar alertas automáticamente
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });
    </script>
</body>
</html>
