<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'MiTec') }} - Innovación Educativa</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Inter:400,600,700,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" xintegrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #ffffff;
            color: #1a202c;
            line-height: 1.6;
            overflow-x: hidden;
        }
        .navbar-welcome {
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid #e2e8f0;
        }
        .navbar-brand-logo {
            height: 40px;
            margin-right: 10px;
        }
        .navbar-brand .brand-text {
            font-size: 1.8rem;
            font-weight: 700;
            color: #34495e;
        }
        .hero-section-creative {
            padding: 4rem 0;
            position: relative;
            background-color: #ffffff;
            overflow: hidden;
        }
        .hero-content {
            position: relative;
            z-index: 2;
        }
        .hero-section-creative h1 {
            font-size: 3.2rem;
            font-weight: 800;
            color: #2c3e50;
            margin-bottom: 1rem;
        }
        .hero-section-creative .lead {
            font-size: 1.15rem;
            color: #555;
            margin-bottom: 2.5rem;
            max-width: 550px;
        }
        .hero-image-container img { /* Estilo para la imagen del hero */
            max-height: 350px; /* Ajusta según necesites */
            width: auto;
            max-width: 100%; /* Asegura que no se desborde en pantallas pequeñas */
            border-radius: 12px;
        }
        .btn-hero-creative {
            padding: 0.75rem 2rem;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .btn-hero-creative.btn-primary {
            background-color: #5468FF;
            border-color: #5468FF;
        }
        .btn-hero-creative.btn-primary:hover {
            background-color: #3A4BCC;
            border-color: #3A4BCC;
            transform: translateY(-2px);
        }
        .btn-hero-creative.btn-outline-secondary {
            color: #5468FF;
            border-color: #5468FF;
        }
        .btn-hero-creative.btn-outline-secondary:hover {
            background-color: #5468FF;
            color: #fff;
        }
        .section-why-mitec {
            padding: 4rem 1rem;
            background-color: #f0f2f5;
        }
        .section-why-mitec .icon-box {
            font-size: 2.5rem;
            color: #5468FF;
            margin-bottom: 1rem;
            display: inline-block;
            padding: 15px;
            background-color: rgba(84, 104, 255, 0.1);
            border-radius: 12px;
        }
        .section-why-mitec h4 {
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .section-why-mitec p {
            color: #495057;
            font-size: 0.95rem;
        }
        .footer-welcome {
            background-color: #2c3e50;
            color: #bdc3c7;
            padding: 2.5rem 0;
        }
        .footer-welcome a {
            color: #ecf0f1;
            font-weight: 500;
        }
        .footer-welcome a:hover {
            color: #5468FF;
        }
    </style>
</head>
<body class="antialiased">
    {{-- Barra de Navegación --}}
    <nav class="navbar navbar-expand-lg navbar-light sticky-top navbar-welcome shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <img src="{{ secure_asset('images/logo_mitec.png') }}" alt="{{ config('app.name', 'MiTec') }} Logo" class="navbar-brand-logo" onerror="this.onerror=null; this.src='{{ secure_asset('images/default_logo.png') }}';">
                <span class="brand-text">{{ config('app.name', 'MiTec') }}</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#welcomeNavbar" aria-controls="welcomeNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="welcomeNavbar">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
                    @if (Route::has('login'))
                        <li class="nav-item">
                            <a href="{{ route('login') }}" class="nav-link fw-semibold me-2">Iniciar Sesión</a>
                        </li>
                    @endif
                    @if (Route::has('register'))
                        <li class="nav-item">
                            <a href="{{ route('register') }}" class="btn btn-primary rounded-pill btn-hero-creative">Registrarse</a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    {{-- Sección Hero Creativa --}}
    <header class="hero-section-creative">
        <div class="container hero-content">
            <div class="row align-items-center">
                <div class="col-lg-6 text-lg-start text-center">
                    <h1 class="display-3">Bienvenido a <br><span class="text-primary">{{ config('app.name', 'MiTec') }}</span></h1>
                    <p class="lead my-4">Tu plataforma digital para una experiencia educativa moderna y conectada en el Instituto Tecnológico.</p>
                    @guest
                    <div class="mt-4">
                        <a href="{{ route('register') }}" class="btn btn-primary btn-lg btn-hero-creative me-sm-2 mb-2 mb-sm-0">
                            <i class="fas fa-user-plus me-2"></i>Regístrate Ahora
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-lg btn-hero-creative">
                            <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                        </a>
                    </div>
                    @else
                     <a href="{{ Auth::user()->roles()->where('nombre', 'docente')->exists() ? route('docente.dashboard') : route('alumno.dashboard') }}" class="btn btn-light btn-lg btn-hero-creative">
                        <i class="fas fa-arrow-right me-2"></i>Ir a mi Panel
                    </a>
                    @endguest
                </div>
                {{-- vvv SECCIÓN DE IMAGEN MODIFICADA vvv --}}
                <div class="col-lg-6 mt-5 mt-lg-0 d-none d-lg-block text-center hero-image-container">
                    {{-- La imagen se llama welcome.png y está en public/images/ --}}
                    <img src="{{ asset('images/welcome.png') }}" class="img-fluid" alt="Plataforma Educativa MiTec">
                </div>
                {{-- ^^^ FIN SECCION DE IMAGEN ^^^ --}}
            </div>
        </div>
    </header>

    {{-- Sección "¿Por qué MiTec?" --}}
    <section class="section-why-mitec">
        <div class="container px-4 py-5">
            <h2 class="text-center display-6 fw-bold mb-5">¿Por qué <span class="text-primary">{{ config('app.name', 'MiTec') }}</span>?</h2>
            <div class="row g-4 gx-lg-5">
                <div class="col-md-4">
                    <div class="text-center benefit-item">
                        <div class="icon-box">
                            <i class="fas fa-book-open"></i>
                        </div>
                        <h4>Acceso Centralizado</h4>
                        <p>Todo tu material de estudio, tareas y seguimiento en un solo lugar.</p>
                    </div>
                </div>
                <div class="col-md-4">
                     <div class="text-center benefit-item">
                        <div class="icon-box" style="background-color: #20c997;">
                            <i class="fas fa-users-cog"></i>
                        </div>
                        <h4>Gestión Eficaz</h4>
                        <p>Herramientas intuitivas para que docentes administren sus cursos y estudiantes organicen su aprendizaje.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center benefit-item">
                        <div class="icon-box" style="background-color: #fd7e14;">
                            <i class="fas fa-comments"></i>
                        </div>
                        <h4>Comunicación Fluida</h4>
                        <p>Facilita la comunicación y el seguimiento entre docentes y estudiantes.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="footer-welcome text-center">
        <div class="container">
            <p class="mb-1">
                <a href="{{ url('/') }}">{{ config('app.name', 'MiTec') }}</a> - Transformando la Educación de los Institutos Tecnológicos.
            </p>
            <p class="mb-0"><small>&copy; {{ date('Y') }} Todos los derechos reservados. Plataforma MiTec.</small></p>
        </div>
    </footer>

    @vite(['resources/js/app.js'])
</body>
</html>
