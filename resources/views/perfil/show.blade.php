@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Mi Perfil</h1>
    <hr>

    {{-- Mensajes Flash --}}
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error_password')) {{-- Para errores específicos del cambio de contraseña --}}
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error_password') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
     @if (session('status_profile')) {{-- Para mensajes de actualización de perfil/foto --}}
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status_profile') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error_profile')) {{-- Para errores de actualización de perfil/foto --}}
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error_profile') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    {{-- Mostrar errores generales de validación para el formulario de perfil (no el de contraseña) --}}
    @if ($errors->any() && !$errors->hasBag('updatePassword'))
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <div class="row">
        {{-- Columna Izquierda: Foto de Perfil y Opción de Cambio --}}
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light fw-bold">Foto de Perfil</div>
                <div class="card-body text-center">
                    {{-- Usar el accesor foto_url del modelo Usuario --}}
                    <img src="{{ $usuario->foto_url }}" alt="Foto de perfil de {{ $usuario->nombre }}" class="img-fluid rounded-circle mb-3 shadow-sm" style="width: 180px; height: 180px; object-fit: cover; border: 3px solid #eee;">

                    {{-- Formulario para cambiar foto de perfil --}}
                    <form action="{{ route('perfil.updatePicture') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <label for="foto_perfil" class="form-label small">Cambiar foto (máx. 2MB, JPG/PNG/GIF)</label>
                            <input class="form-control form-control-sm @error('foto_perfil') is-invalid @enderror" type="file" id="foto_perfil" name="foto_perfil" accept="image/jpeg,image/png,image/gif">
                            @error('foto_perfil')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-sm btn-outline-primary w-100">Actualizar Foto</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Columna Derecha: Información y Cambio de Contraseña --}}
        <div class="col-lg-8 mb-4">
            {{-- Tarjeta Información Básica (Formulario para editar) --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-light fw-bold">Información Básica</div>
                <div class="card-body">
                    <form action="{{ route('perfil.update') }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nombre" class="form-label">Nombre(s) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{ old('nombre', $usuario->nombre) }}" required>
                                @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="apellidos" class="form-label">Apellidos</label>
                                <input type="text" class="form-control @error('apellidos') is-invalid @enderror" id="apellidos" name="apellidos" value="{{ old('apellidos', $usuario->apellidos) }}">
                                @error('apellidos') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $usuario->email) }}" required>
                             @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="tel" class="form-control @error('telefono') is-invalid @enderror" id="telefono" name="telefono" value="{{ old('telefono', $usuario->telefono) }}">
                                @error('telefono') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="genero" class="form-label">Género</label>
                                <select class="form-select @error('genero') is-invalid @enderror" id="genero" name="genero">
                                    <option value="">-- Seleccionar --</option>
                                    {{-- Usamos old() con fallback al valor actual del usuario, o 'no_especificado' si es null --}}
                                    <option value="masculino" {{ old('genero', $usuario->genero ?? 'no_especificado') == 'masculino' ? 'selected' : '' }}>Masculino</option>
                                    <option value="femenino" {{ old('genero', $usuario->genero ?? 'no_especificado') == 'femenino' ? 'selected' : '' }}>Femenino</option>
                                    <option value="otro" {{ old('genero', $usuario->genero ?? 'no_especificado') == 'otro' ? 'selected' : '' }}>Otro</option>
                                    <option value="no_especificado" {{ old('genero', $usuario->genero ?? 'no_especificado') == 'no_especificado' ? 'selected' : '' }}>Prefiero no decirlo</option>
                                </select>
                                @error('genero') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <p class="small text-muted mt-2">Rol(es): {{ $usuario->roles->pluck('nombre')->map(fn($rol) => ucfirst($rol))->implode(', ') }}</p>
                        <p class="small text-muted">Miembro desde: {{ $usuario->created_at->format('d/m/Y') }}</p>

                        <button type="submit" class="btn btn-success">Guardar Cambios de Perfil</button>
                    </form>
                </div>
            </div>

            {{-- Tarjeta Cambiar Contraseña --}}
            <div class="card shadow-sm">
                <div class="card-header bg-light fw-bold">Cambiar Contraseña</div>
                <div class="card-body">
                    <form action="{{ route('perfil.updatePassword') }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label for="current_password" class="form-label">Contraseña Actual <span class="text-danger">*</span></label>
                            <input type="password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" id="current_password" name="current_password" required>
                            @error('current_password', 'updatePassword') {{-- Especificar el error bag --}}
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Nueva Contraseña <span class="text-danger">*</span></label>
                            <input type="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" id="password" name="password" required>
                             @error('password', 'updatePassword')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="passwordHelpBlock" class="form-text">
                              Debe tener al menos 8 caracteres y se recomiendan símbolos y números.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirmar Nueva Contraseña <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Actualizar Contraseña</button>
                    </form>
                </div>
            </div>
        </div>
    </div> {{-- Fin row --}}
</div> {{-- Fin container --}}
@endsection
