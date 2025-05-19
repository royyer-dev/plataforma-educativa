@extends('layouts.app') {{-- Usa el layout base de tu aplicación --}}

@section('content')
<div class="container py-5"> {{-- Añadido padding general --}}
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6"> {{-- Ajustado ancho para un formulario más compacto --}}
            <div class="card shadow-lg border-0"> {{-- Tarjeta con más sombra y sin borde --}}
                <div class="card-header bg-primary text-white text-center"> {{-- Encabezado con color primario --}}
                    <h4 class="mb-0 py-2"><i class="fas fa-user-plus me-2"></i>{{ __('Registro de Nuevo Usuario') }}</h4>
                </div>

                <div class="card-body p-4 p-md-5"> {{-- Más padding interno --}}
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        {{-- Campo Nombre --}}
                        <div class="mb-3">
                            <label for="nombre" class="form-label fw-bold">{{ __('Nombre(s)') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input id="nombre" type="text" class="form-control @error('nombre') is-invalid @enderror" name="nombre" value="{{ old('nombre') }}" required autocomplete="given-name" autofocus placeholder="Ingresa tu nombre o nombres">
                            </div>
                            @error('nombre')
                                <span class="invalid-feedback d-block" role="alert"> {{-- d-block para que se muestre bien --}}
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        {{-- Campo Apellidos --}}
                        <div class="mb-3">
                            <label for="apellidos" class="form-label fw-bold">{{ __('Apellidos') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user-friends"></i></span>
                                <input id="apellidos" type="text" class="form-control @error('apellidos') is-invalid @enderror" name="apellidos" value="{{ old('apellidos') }}" autocomplete="family-name" placeholder="Ingresa tus apellidos (opcional)">
                            </div>
                            @error('apellidos')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        {{-- Campo Teléfono --}}
                        <div class="mb-3">
                            <label for="telefono" class="form-label fw-bold">{{ __('Teléfono (Opcional)') }}</label>
                             <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                <input id="telefono" type="tel" class="form-control @error('telefono') is-invalid @enderror" name="telefono" value="{{ old('telefono') }}" autocomplete="tel" placeholder="Ej: 3121234567">
                            </div>
                            @error('telefono')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        {{-- vvv CAMPO GÉNERO AÑADIDO vvv --}}
                        <div class="mb-3">
                            <label for="genero" class="form-label fw-bold">{{ __('Género') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-venus-mars"></i></span>
                                <select id="genero" class="form-select @error('genero') is-invalid @enderror" name="genero">
                                    <option value="" {{ old('genero') == '' ? 'selected' : '' }}>-- Selecciona tu género (Opcional) --</option>
                                    <option value="masculino" {{ old('genero') == 'masculino' ? 'selected' : '' }}>Masculino</option>
                                    <option value="femenino" {{ old('genero') == 'femenino' ? 'selected' : '' }}>Femenino</option>
                                    <option value="otro" {{ old('genero') == 'otro' ? 'selected' : '' }}>Otro</option>
                                    <option value="no_especificado" {{ old('genero', 'no_especificado') == 'no_especificado' ? 'selected' : '' }}>Prefiero no decirlo</option>
                                </select>
                            </div>
                            @error('genero')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        {{-- ^^^ FIN CAMPO GÉNERO ^^^ --}}

                        {{-- Campo Email --}}
                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold">{{ __('Correo Electrónico') }} <span class="text-danger">*</span></label>
                             <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="tu.correo@ejemplo.com">
                            </div>
                            @error('email')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        {{-- Selector de Rol --}}
                        <div class="mb-3">
                            <label for="role_id" class="form-label fw-bold">{{ __('Registrarse como') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                                <select id="role_id" class="form-select @error('role_id') is-invalid @enderror" name="role_id" required>
                                    <option value="" disabled {{ old('role_id') ? '' : 'selected' }}>-- Selecciona un rol --</option>
                                    @if(isset($roles))
                                        @foreach ($roles as $id => $nombre)
                                            <option value="{{ $id }}" {{ old('role_id') == $id ? 'selected' : '' }}>
                                                {{ ucfirst($nombre) }}
                                            </option>
                                        @endforeach
                                    @else {{-- Fallback si $roles no está disponible --}}
                                        <option value="2">Estudiante</option> {{-- Asume ID 2 para Estudiante --}}
                                        <option value="1">Docente</option>   {{-- Asume ID 1 para Docente --}}
                                    @endif
                                </select>
                            </div>
                            @error('role_id')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        {{-- Campo Password --}}
                        <div class="mb-3">
                            <label for="password" class="form-label fw-bold">{{ __('Contraseña') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Mínimo 8 caracteres">
                            </div>
                            @error('password')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                             <div id="passwordHelpBlock" class="form-text">
                              Debe tener al menos 8 caracteres. Se recomienda usar números y símbolos.
                            </div>
                        </div>

                        {{-- Campo Confirmar Password --}}
                        <div class="mb-4"> {{-- Aumentado margen inferior --}}
                            <label for="password-confirm" class="form-label fw-bold">{{ __('Confirmar Contraseña') }} <span class="text-danger">*</span></label>
                             <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="Repite tu contraseña">
                            </div>
                        </div>

                        {{-- Botón de Registro --}}
                        <div class="d-grid mb-3"> {{-- d-grid para botón de ancho completo --}}
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-user-check me-2"></i>{{ __('Registrarse') }}
                            </button>
                        </div>

                        <div class="text-center">
                            <small>¿Ya tienes una cuenta? <a href="{{ route('login') }}">Inicia Sesión Aquí</a></small>
                        </div>
                    </form>
                </div> {{-- Fin card-body --}}
            </div> {{-- Fin card --}}
        </div> {{-- Fin col --}}
    </div> {{-- Fin row --}}
</div> {{-- Fin container --}}
@endsection
