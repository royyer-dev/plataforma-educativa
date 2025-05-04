@extends('layouts.app') {{-- Usa el layout base de tu aplicación --}}

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Registro de Usuario') }}</div> {{-- Título modificado --}}

                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        {{-- Campo Nombre --}}
                        <div class="row mb-3">
                            <label for="nombre" class="col-md-4 col-form-label text-md-end">{{ __('Nombre(s)') }}</label> {{-- Etiqueta modificada --}}

                            <div class="col-md-6">
                                {{-- Input: id y name cambiados a 'nombre', @error y old() también --}}
                                <input id="nombre" type="text" class="form-control @error('nombre') is-invalid @enderror" name="nombre" value="{{ old('nombre') }}" required autocomplete="given-name" autofocus>

                                @error('nombre') {{-- Error para 'nombre' --}}
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        {{-- Campo Apellidos (Nuevo) --}}
                        <div class="row mb-3">
                            <label for="apellidos" class="col-md-4 col-form-label text-md-end">{{ __('Apellidos') }}</label>

                            <div class="col-md-6">
                                <input id="apellidos" type="text" class="form-control @error('apellidos') is-invalid @enderror" name="apellidos" value="{{ old('apellidos') }}" autocomplete="family-name"> {{-- 'required' es opcional aquí, quitado por defecto --}}

                                @error('apellidos')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        {{-- Campo Teléfono (Nuevo - Opcional) --}}
                        <div class="row mb-3">
                            <label for="telefono" class="col-md-4 col-form-label text-md-end">{{ __('Teléfono (Opcional)') }}</label>

                            <div class="col-md-6">
                                <input id="telefono" type="tel" class="form-control @error('telefono') is-invalid @enderror" name="telefono" value="{{ old('telefono') }}" autocomplete="tel"> {{-- type="tel", sin 'required' --}}

                                @error('telefono')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        {{-- Campo Email (Sin cambios) --}}
                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Correo Electrónico') }}</label> {{-- Etiqueta modificada --}}

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        {{-- Selector de Rol (Nuevo) --}}
                        <div class="row mb-3">
                            <label for="role_id" class="col-md-4 col-form-label text-md-end">{{ __('Registrarse como') }}</label>

                            <div class="col-md-6">
                                {{-- Usamos form-select de Bootstrap para el <select> --}}
                                <select id="role_id" class="form-select @error('role_id') is-invalid @enderror" name="role_id" required>
                                    <option value="" disabled {{ old('role_id') ? '' : 'selected' }}>-- Selecciona un rol --</option>
                                    {{-- Verifica si la variable $roles fue pasada desde el controlador --}}
                                    @if(isset($roles))
                                        @foreach ($roles as $id => $nombre)
                                            {{-- Muestra cada rol como una opción --}}
                                            <option value="{{ $id }}" {{ old('role_id') == $id ? 'selected' : '' }}>
                                                {{ ucfirst($nombre) }} {{-- Pone la primera letra en mayúscula --}}
                                            </option>
                                        @endforeach
                                    @else
                                         <option value="" disabled>No hay roles disponibles</option>
                                    @endif
                                </select>

                                @error('role_id') {{-- Muestra error si la validación del rol falla --}}
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        {{-- Fin Selector de Rol --}}


                        {{-- Campo Password (Sin cambios) --}}
                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Contraseña') }}</label> {{-- Etiqueta modificada --}}

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        {{-- Campo Confirmar Password (Sin cambios) --}}
                        <div class="row mb-3">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-end">{{ __('Confirmar Contraseña') }}</label> {{-- Etiqueta modificada --}}

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        {{-- Botón de Registro (Sin cambios) --}}
                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Registrarse') }} {{-- Texto modificado --}}
                                </button>
                            </div>
                        </div>
                    </form>
                </div> {{-- Fin card-body --}}
            </div> {{-- Fin card --}}
        </div> {{-- Fin col-md-8 --}}
    </div> {{-- Fin row --}}
</div> {{-- Fin container --}}
@endsection