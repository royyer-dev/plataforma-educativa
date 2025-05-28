@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0 py-2">
                        <i class="fas fa-envelope-open-text me-2"></i>{{ __('Verifica tu Correo Electrónico') }}
                    </h4>
                </div>

                <div class="card-body p-4 p-md-5 text-center">
                    @if (session('resent'))
                        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ __('Se ha enviado un nuevo enlace de verificación a tu correo electrónico.') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="mb-4">
                        <img src="{{ asset('images/email-verify.svg') }}" alt="Verificación de Email" 
                             class="img-fluid mb-4" style="max-width: 200px;">
                        
                        <p class="lead">{{ __('Antes de continuar, revisa tu correo electrónico para encontrar el enlace de verificación.') }}</p>
                        <p class="text-muted">
                            {{ __('Si no has recibido el correo') }}, 
                            <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                                @csrf
                                <button type="submit" class="btn btn-link p-0">
                                    {{ __('haz clic aquí para solicitar otro') }}
                                </button>.
                            </form>
                        </p>
                    </div>

                    <hr class="my-4">

                    <div class="text-center">
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Volver al Inicio
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
