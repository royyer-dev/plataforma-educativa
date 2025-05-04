<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Plataforma Educativa') }}</title> {{-- Título más específico --}}

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=Nunito:400,600,700" rel="stylesheet">

        {{-- Incluir CSS de Bootstrap via Vite (asume configuración estándar) --}}
        @vite(['resources/sass/app.scss'])

        {{-- Estilos personalizados opcionales --}}
        <style>
            body {
                font-family: 'Nunito', sans-serif;
                background-color: #f8fafc; /* Un fondo claro */
            }
            .hero-section {
                background-color: #4a5568; /* Un gris azulado oscuro */
                color: white;
                padding: 4rem 0;
                text-align: center;
            }
            .navbar-custom {
                 background-color: rgba(255, 255, 255, 0.9); /* Blanco semi-transparente */
                 backdrop-filter: blur(5px); /* Efecto blur si el navegador lo soporta */
            }
            .feature-icon {
                font-size: 2.5rem;
                color: #6366f1; /* Un color índigo */
            }
        </style>
        {{-- Font Awesome (si quieres usar iconos) --}}
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    </head>
    <body class="antialiased">
        {{-- Barra de Navegación Simple --}}
        <nav class="navbar navbar-expand-md navbar-light sticky-top navbar-custom shadow-sm">
            <div class="container">
                 <a class="navbar-brand fw-bold" href="{{ url('/') }}">
                    {{ config('app.name', 'Plataforma EDU') }} {{-- Puedes poner el nombre de tu app --}}
                </a>
                 <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#welcomeNavbar" aria-controls="welcomeNavbar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="welcomeNavbar">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        @if (Route::has('login'))
                            <li class="nav-item">
                                <a href="{{ route('login') }}" class="nav-link">Iniciar Sesión</a>
                            </li>
                        @endif

                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a href="{{ route('register') }}" class="nav-link">Registrarse</a>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </nav>

        {{-- Sección Hero --}}
        <div class="hero-section">
            <div class="container">
                <h1 class="display-4 fw-bold">Bienvenido a tu Plataforma Educativa</h1>
                <p class="lead my-3">Un espacio diseñado para facilitar la enseñanza y el aprendizaje en línea, gestionado por tu propia institución.</p>
                @guest
                <a href="{{ route('register') }}" class="btn btn-primary btn-lg mt-3">¡Comienza Ahora!</a>
                @else
                 <a href="{{ Auth::user()->roles()->where('nombre', 'docente')->exists() ? route('docente.dashboard') : route('alumno.dashboard') }}" class="btn btn-light btn-lg mt-3">Ir a mi Panel</a>
                @endguest
            </div>
        </div>

        {{-- Sección de Características (Opcional) --}}
        <div class="container px-4 py-5">
            <h2 class="pb-2 border-bottom text-center">Características Principales</h2>
            <div class="row g-4 py-5 row-cols-1 row-cols-lg-3">
                <div class="col d-flex align-items-start">
                    <div class="text-dark flex-shrink-0 me-3">
                         <i class="fas fa-chalkboard-teacher feature-icon"></i> {{-- Icono Docente --}}
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0">Gestión Docente</h4>
                        <p>Crea y administra cursos, módulos, materiales y tareas fácilmente.</p>
                    </div>
                </div>
                <div class="col d-flex align-items-start">
                     <div class="text-dark flex-shrink-0 me-3">
                         <i class="fas fa-user-graduate feature-icon"></i> {{-- Icono Estudiante --}}
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0">Acceso Estudiantil</h4>
                        <p>Consulta cursos, accede a materiales, entrega tareas y revisa tus calificaciones.</p>
                    </div>
                </div>
                <div class="col d-flex align-items-start">
                    <div class="text-dark flex-shrink-0 me-3">
                         <i class="fas fa-comments feature-icon"></i> {{-- Icono Comunicación --}}
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0">Comunicación</h4>
                        <p>Mantente informado con notificaciones sobre cursos y tareas.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer Simple --}}
        <footer class="py-3 mt-4 border-top bg-light">
            <p class="text-center text-muted">&copy; {{ date('Y') }} {{ config('app.name', 'Plataforma Educativa') }}. Todos los derechos reservados.</p>
        </footer>

        {{-- Scripts de Bootstrap via Vite --}}
        @vite(['resources/js/app.js'])
    </body>
</html>
