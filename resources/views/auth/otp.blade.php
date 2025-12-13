@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">Verificação OTP</h5>
                    <p class="text-muted">Introduza o código de 6 dígitos enviado por SMS.</p>
                    <form method="POST" action="{{ route('otp.verify') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Código OTP</label>
                            <input type="text" name="code" maxlength="6" class="form-control text-center" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Validar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
