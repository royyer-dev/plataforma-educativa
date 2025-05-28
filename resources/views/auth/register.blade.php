@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0 py-2">
                        <i class="fas fa-user-plus me-2"></i>{{ __('Registro de Nuevo Usuario') }}
                    </h4>
                </div>

                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <div class="rounded-circle bg-light p-3 d-inline-block mb-3">
                            <i class="fas fa-user-circle fa-2x text-primary"></i>
                        </div>
                        <p class="lead">Completa el formulario para crear tu cuenta</p>
                    </div>

                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="nombre" class="form-label fw-bold">{{ __('Nombre(s)') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input id="nombre" type="text" 
                                       class="form-control @error('nombre') is-invalid @enderror" 
                                       name="nombre" value="{{ old('nombre') }}" required 
                                       autocomplete="given-name" autofocus
                                       placeholder="Ingresa tu nombre o nombres">
                            </div>
                            @error('nombre')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="apellidos" class="form-label fw-bold">{{ __('Apellidos') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user-friends"></i></span>
                                <input id="apellidos" type="text" 
                                       class="form-control @error('apellidos') is-invalid @enderror" 
                                       name="apellidos" value="{{ old('apellidos') }}" 
                                       autocomplete="family-name"
                                       placeholder="Ingresa tus apellidos (opcional)">
                            </div>
                            @error('apellidos')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="telefono" class="form-label fw-bold">{{ __('Teléfono (Opcional)') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                <input id="telefono" type="tel" 
                                       class="form-control @error('telefono') is-invalid @enderror" 
                                       name="telefono" value="{{ old('telefono') }}"
                                       placeholder="Ej: 123-456-7890">
                            </div>
                            @error('telefono')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="genero" class="form-label fw-bold">{{ __('Género') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-venus-mars"></i></span>
                                <select id="genero" class="form-select @error('genero') is-invalid @enderror" 
                                        name="genero">
                                    <option value="">Selecciona una opción</option>
                                    <option value="masculino" {{ old('genero') == 'masculino' ? 'selected' : '' }}>Masculino</option>
                                    <option value="femenino" {{ old('genero') == 'femenino' ? 'selected' : '' }}>Femenino</option>
                                    <option value="otro" {{ old('genero') == 'otro' ? 'selected' : '' }}>Otro</option>
                                    <option value="no_especificado" {{ old('genero') == 'no_especificado' ? 'selected' : '' }}>Prefiero no decir</option>
                                </select>
                            </div>
                            @error('genero')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold">{{ __('Correo Electrónico') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input id="email" type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       name="email" value="{{ old('email') }}" required 
                                       autocomplete="email"
                                       placeholder="tu.correo@ejemplo.com">
                            </div>
                            @error('email')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="role_id" class="form-label fw-bold">{{ __('Registrarse como') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                                <select id="role_id" class="form-select @error('role_id') is-invalid @enderror" 
                                        name="role_id" required>
                                    <option value="">Selecciona un rol</option>
                                    <option value="2" {{ old('role_id') == 2 ? 'selected' : '' }}>Docente</option>
                                    <option value="3" {{ old('role_id') == 3 ? 'selected' : '' }}>Estudiante</option>
                                </select>
                            </div>
                            @error('role_id')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-bold">{{ __('Contraseña') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input id="password" type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       name="password" required autocomplete="new-password"
                                       placeholder="Mínimo 8 caracteres">
                            </div>
                            @error('password')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password-confirm" class="form-label fw-bold">{{ __('Confirmar Contraseña') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input id="password-confirm" type="password" class="form-control" 
                                       name="password_confirmation" required autocomplete="new-password"
                                       placeholder="Repite tu contraseña">
                            </div>
                        </div>

                        <div class="d-grid gap-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-user-plus me-2"></i>{{ __('Crear Cuenta') }}
                            </button>
                            <div class="text-center">
                                <small>¿Ya tienes una cuenta? <a href="{{ route('login') }}" class="text-decoration-none">Inicia Sesión Aquí</a></small>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
