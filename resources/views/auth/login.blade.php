@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0 py-2">
                        <i class="fas fa-sign-in-alt me-2"></i>{{ __('Iniciar Sesión') }}
                    </h4>
                </div>

                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <div class="rounded-circle bg-light p-3 d-inline-block mb-3">
                            <i class="fas fa-user-circle fa-2x text-primary"></i>
                        </div>
                        <p class="lead">Bienvenido de nuevo a MiTec</p>
                    </div>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        {{-- Campo Email --}}
                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold">
                                {{ __('Correo Electrónico') }}
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input id="email" type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       name="email" value="{{ old('email') }}" 
                                       required autocomplete="email" autofocus
                                       placeholder="tu.correo@ejemplo.com">
                            </div>
                            @error('email')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        {{-- Campo Password --}}
                        <div class="mb-4">
                            <label for="password" class="form-label fw-bold">
                                {{ __('Contraseña') }}
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input id="password" type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       name="password" required 
                                       autocomplete="current-password"
                                       placeholder="Ingresa tu contraseña">
                            </div>
                            @error('password')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        {{-- Opciones adicionales --}}
                        <div class="row align-items-center mb-4">
                            <div class="col">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" 
                                           name="remember" id="remember" 
                                           {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="remember">
                                        {{ __('Recordarme') }}
                                    </label>
                                </div>
                            </div>
                            @if (Route::has('password.request'))
                                <div class="col text-end">
                                    <a class="btn btn-link btn-sm p-0" href="{{ route('password.request') }}">
                                        {{ __('¿Olvidaste tu contraseña?') }}
                                    </a>
                                </div>
                            @endif
                        </div>

                        {{-- Botón de Login --}}
                        <div class="d-grid gap-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>{{ __('Iniciar Sesión') }}
                            </button>

                            {{-- Enlace a Registro --}}
                            @if (Route::has('register'))
                                <div class="text-center">
                                    <small>¿No tienes una cuenta? <a href="{{ route('register') }}" class="text-decoration-none">Regístrate Aquí</a></small>
                                </div>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            {{-- Tarjeta de ayuda --}}
            <div class="card mt-4 border-0 bg-light">
                <div class="card-body text-center py-3">
                    <small class="text-muted">
                        <i class="fas fa-question-circle me-1"></i>
                        ¿Necesitas ayuda? Contacta a soporte técnico
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.form-control {
    border: 1px solid #dee2e6;
}

.form-control:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1);
}

.input-group-text {
    border: 1px solid #dee2e6;
    background-color: #f8f9fa;
}

.btn-lg {
    padding: 0.75rem 1.25rem;
}

.invalid-feedback {
    font-size: 0.875rem;
}

.btn-link {
    text-decoration: none;
}

.btn-link:hover {
    text-decoration: underline;
}
</style>
@endsection
