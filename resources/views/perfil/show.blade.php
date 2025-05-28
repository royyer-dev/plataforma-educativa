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
                <div class="card-body text-center">
                    <div class="profile-picture mb-3">
                        <img src="{{ $usuario->foto_url }}" alt="Foto de perfil de {{ $usuario->nombre }}">
                        <button type="button" class="change-photo" data-bs-toggle="modal" data-bs-target="#changePhotoModal">
                            <i class="fas fa-camera"></i>
                        </button>
                    </div>
                    <h5 class="mb-0">{{ $usuario->nombre }} {{ $usuario->apellidos }}</h5>
                    <p class="text-muted">{{ $usuario->roles->pluck('nombre')->map(fn($rol) => ucfirst($rol))->implode(', ') }}</p>
                </div>
            </div>

            {{-- Botones de Acciones de Cuenta --}}
            <div class="d-grid gap-2 mt-3">
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#cambiarPasswordModal">
                    <i class="fas fa-key me-1"></i> Cambiar Contraseña
                </button>
            </div>
        </div>

        {{-- Columna Derecha: Información Básica --}}
        <div class="col-lg-8 mb-4">
            {{-- Tarjeta Información Básica (Formulario para editar) --}}
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Información Personal</h5>
                </div>
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
                        <p class="small text-muted">Miembro desde: {{ $usuario->created_at->format('d/m/Y') }}</p>                        <div class="mt-4 d-flex align-items-center justify-content-between">
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Guardar Cambios
                                </button>
                            </div>
                            <div>
                                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#eliminarCuentaModal">
                                    <i class="fas fa-user-slash me-1"></i> Eliminar cuenta
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Cambiar Contraseña --}}
<div class="modal fade" id="cambiarPasswordModal" tabindex="-1" aria-labelledby="cambiarPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cambiarPasswordModalLabel">Cambiar Contraseña</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('perfil.updatePassword') }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Contraseña Actual <span class="text-danger">*</span></label>
                        <input type="password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" 
                               id="current_password" name="current_password" required>
                        @error('current_password', 'updatePassword')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Nueva Contraseña <span class="text-danger">*</span></label>
                        <input type="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" 
                               id="password" name="password" required>
                        @error('password', 'updatePassword')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Debe tener al menos 8 caracteres y se recomiendan símbolos y números.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirmar Nueva Contraseña <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar Contraseña</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Eliminar Cuenta --}}
<div class="modal fade" id="eliminarCuentaModal" tabindex="-1" aria-labelledby="eliminarCuentaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="eliminarCuentaModalLabel">Confirmar Eliminación de Cuenta</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('perfil.destroy') }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Advertencia:</strong> Esta acción no se puede deshacer.
                    </div>
                    <p>Al eliminar tu cuenta:</p>
                    <ul>
                        <li>Se eliminarán todos tus datos personales</li>
                        <li>Si eres docente, se eliminarán todos tus cursos y materiales</li>
                        <li>Si eres estudiante, se eliminarán tus inscripciones y entregas</li>
                        <li>No podrás recuperar esta información</li>
                    </ul>
                    <div class="mb-3">
                        <label for="password_confirmation_delete" class="form-label">Confirma tu contraseña para continuar <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="password_confirmation_delete" name="password_confirmation" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-user-slash me-1"></i> Eliminar mi cuenta permanentemente
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal para Cambio de Foto --}}
<div class="modal fade" id="changePhotoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cambiar Foto de Perfil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('perfil.updatePicture') }}" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    @csrf
                    @method('PATCH')
                    <div class="mb-3">
                        <label for="foto_perfil" class="form-label">Seleccionar Nueva Foto</label>
                        <input type="file" class="form-control" id="foto_perfil" name="foto_perfil" accept="image/*" required>
                        <div class="form-text">Formatos permitidos: JPG, PNG, GIF. Tamaño máximo: 2MB</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar Foto</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Estilos generales */
    .profile-section {
        transition: all 0.3s ease;
    }

    /* Foto de perfil */
    .profile-picture {
        position: relative;
        display: inline-block;
    }
    .profile-picture img {
        width: 180px;
        height: 180px;
        object-fit: cover;
        border-radius: 50%;
        border: 3px solid #fff;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    .profile-picture:hover img {
        border-color: #0d6efd;
    }
    .profile-picture .change-photo {
        position: absolute;
        bottom: 0;
        right: 0;
        background: #fff;
        border: 2px solid #0d6efd;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #0d6efd;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .profile-picture .change-photo:hover {
        background: #0d6efd;
        color: #fff;
    }

    /* Campos de formulario */
    .form-control, .form-select {
        border: 1px solid #e2e8f0;
        padding: 0.75rem 1rem;
        border-radius: 0.5rem;
        transition: all 0.2s ease;
    }
    .form-control:focus, .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13,110,253,0.15);
    }

    /* Tarjetas */
    .card {
        border: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        border-radius: 1rem;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    .card:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .card-header {
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        padding: 1rem 1.5rem;
    }

    /* Botones */
    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    .btn:hover {
        transform: translateY(-1px);
    }
    
    /* Modal de cambio de contraseña */
    .modal-content {
        border: none;
        border-radius: 1rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .modal-header {
        border-bottom: 1px solid #e9ecef;
        background: #f8f9fa;
    }
    .modal-footer {
        border-top: 1px solid #e9ecef;
        background: #f8f9fa;
    }

    /* Sección de danger zone */
    .danger-zone {
        border: 1px solid #dc3545;
        border-radius: 1rem;
        padding: 1.5rem;
        margin-top: 2rem;
    }
    .danger-zone h5 {
        color: #dc3545;
        margin-bottom: 1rem;
    }
    .danger-zone p {
        color: #6c757d;
        font-size: 0.9rem;
    }

    .modal-header .btn-close-white {
        filter: brightness(0) invert(1);
    }
</style>
@endpush
