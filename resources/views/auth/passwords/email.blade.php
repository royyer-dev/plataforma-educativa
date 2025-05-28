@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0 py-2">
                        <i class="fas fa-key me-2"></i>{{ __('Restablecer Contraseña') }}
                    </h4>
                </div>

                <div class="card-body p-4 p-md-5">
                    @if (session('status'))
                        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('status') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="text-center mb-4">
                        <div class="rounded-circle bg-light p-3 d-inline-block mb-3">
                            <i class="fas fa-envelope fa-2x text-primary"></i>
                        </div>
                        <p class="lead">Ingresa tu correo electrónico y te enviaremos las instrucciones para restablecer tu contraseña.</p>
                    </div>

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="email" class="form-label fw-bold">{{ __('Correo Electrónico') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input id="email" type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       name="email" value="{{ old('email') }}" required 
                                       autocomplete="email" autofocus
                                       placeholder="tu.correo@ejemplo.com">
                            </div>
                            @error('email')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="d-grid gap-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane me-2"></i>{{ __('Enviar Enlace de Restablecimiento') }}
                            </button>

                            <a href="{{ route('login') }}" class="btn btn-link">
                                <i class="fas fa-arrow-left me-2"></i>Volver al Login
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
