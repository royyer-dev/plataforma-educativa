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
    {{-- Mostrar errores generales si los hubiera --}}
    @if ($errors->any() && !$errors->has('password_confirmacion')) {{-- Evitar mostrar error de contraseña aquí si viene de otro form --}}
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <div class="row">
        {{-- Columna Información Básica --}}
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">Información Básica</div>
                <div class="card-body">
                     {{-- Aquí podríamos poner un formulario para editar nombre/email en el futuro --}}
                     <dl class="row">
                        <dt class="col-sm-4">Nombre:</dt>
                        <dd class="col-sm-8">{{ $usuario->nombre }} {{ $usuario->apellidos }}</dd>

                        <dt class="col-sm-4">Email:</dt>
                        <dd class="col-sm-8">{{ $usuario->email }}</dd>

                        <dt class="col-sm-4">Teléfono:</dt>
                        <dd class="col-sm-8">{{ $usuario->telefono ?? '--' }}</dd>

                        <dt class="col-sm-4">Rol(es):</dt>
                        <dd class="col-sm-8">
                            {{ $usuario->roles->pluck('nombre')->map(fn($rol) => ucfirst($rol))->implode(', ') }}
                        </dd>

                         <dt class="col-sm-4">Miembro desde:</dt>
                        <dd class="col-sm-8">{{ $usuario->created_at->format('d/m/Y') }}</dd>
                    </dl>
                    {{-- Botón para editar perfil (futuro) --}}
                    {{-- <button type="button" class="btn btn-outline-secondary btn-sm" disabled>Editar Perfil</button> --}}
                </div>
            </div>
        </div>

        {{-- Columna Cambiar Contraseña --}}
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">Cambiar Contraseña</div>
                <div class="card-body">
                    <form action="{{ route('perfil.updatePassword') }}" method="POST">
                        @csrf
                        @method('PATCH') {{-- Coincide con la ruta definida --}}

                        {{-- Contraseña Actual --}}
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Contraseña Actual <span class="text-danger">*</span></label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Nueva Contraseña --}}
                        <div class="mb-3">
                            <label for="password" class="form-label">Nueva Contraseña <span class="text-danger">*</span></label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="passwordHelpBlock" class="form-text">
                              Debe tener al menos 8 caracteres.
                            </div>
                        </div>

                        {{-- Confirmar Nueva Contraseña --}}
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirmar Nueva Contraseña <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                            {{-- El error de confirmación se muestra bajo el campo 'password' --}}
                        </div>

                        <button type="submit" class="btn btn-primary">Actualizar Contraseña</button>
                    </form>
                </div>
            </div>
        </div>
    </div> {{-- Fin row --}}
</div> {{-- Fin container --}}
@endsection
