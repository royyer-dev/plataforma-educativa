@extends('layouts.app')

@section('content')
<div class="container py-5"> {{-- Añadido padding general --}}
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-5"> {{-- Ajustado ancho para un formulario más compacto --}}
            <div class="card shadow-lg border-0"> {{-- Tarjeta con más sombra y sin borde --}}
                <div class="card-header bg-primary text-white text-center"> {{-- Encabezado con color primario --}}
                    <h4 class="mb-0 py-2"><i class="fas fa-sign-in-alt me-2"></i>{{ __('Iniciar Sesión en MiTec') }}</h4>
                </div>

                <div class="card-body p-4 p-md-5"> {{-- Más padding interno --}}
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        {{-- Campo Email --}}
                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold">{{ __('Correo Electrónico') }}</label>
                            <div class="input-group"> {{-- Input group para icono --}}
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="tu.correo@ejemplo.com">
                            </div>
                            @error('email')
                                <span class="invalid-feedback d-block" role="alert"> {{-- d-block para asegurar visibilidad --}}
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        {{-- Campo Password --}}
                        <div class="mb-3">
                            <label for="password" class="form-label fw-bold">{{ __('Contraseña') }}</label>
                            <div class="input-group"> {{-- Input group para icono --}}
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Ingresa tu contraseña">
                            </div>
                            @error('password')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        {{-- Checkbox Recordarme y Enlace Olvidé Contraseña --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="remember">
                                        {{ __('Recordarme') }}
                                    </label>
                                </div>
                            </div>
                            @if (Route::has('password.request'))
                                <div class="col-md-6 text-md-end">
                                    <a class="btn btn-link btn-sm p-0" href="{{ route('password.request') }}">
                                        {{ __('¿Olvidaste tu contraseña?') }}
                                    </a>
                                </div>
                            @endif
                        </div>

                        {{-- Botón de Login --}}
                        <div class="d-grid mb-3"> {{-- d-grid para botón de ancho completo --}}
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>{{ __('Iniciar Sesión') }}
                            </button>
                        </div>

                        {{-- Enlace a Registro --}}
                        @if (Route::has('register'))
                            <div class="text-center">
                                <small>¿No tienes una cuenta? <a href="{{ route('register') }}">Regístrate Aquí</a></small>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
