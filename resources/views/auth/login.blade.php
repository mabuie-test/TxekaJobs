@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">Login seguro</h5>
                    <p class="text-muted">Autentique-se com email ou telefone e receba um OTP por SMS.</p>
                    <form method="POST" action="{{ route('login.post') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Email ou telefone</label>
                            <input type="text" name="login" value="{{ old('login') }}" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <input type="hidden" name="device_fingerprint" value="">
                        <button type="submit" class="btn btn-primary w-100">Continuar</button>
                    </form>
                    <hr class="my-3">
                    <div class="d-grid gap-2">
                        <a href="{{ route('register.cliente') }}" class="btn btn-outline-secondary">Quero criar conta de cliente</a>
                        <a href="{{ route('register.prestador') }}" class="btn btn-outline-success">Quero ser prestador</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const input = document.querySelector('input[name="device_fingerprint"]');
            if (input) {
                input.value = btoa(navigator.userAgent + '|' + (window.screen?.width || '') + 'x' + (window.screen?.height || ''));
            }
        });
    </script>
@endsection
