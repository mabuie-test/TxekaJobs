@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h1 class="h4 mb-3">Registar-se como Prestador</h1>
                    <p class="text-muted">Comece a receber pedidos filtrados por categoria e zona. Leads pagas via subscrição ou avulso.</p>

                    <form method="POST" action="{{ route('register.prestador.store') }}" novalidate>
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nome</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                                @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Telemóvel</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" required>
                                @error('phone')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email (opcional)</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                                @error('email')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                                @error('password')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Confirmar Password</label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Bio</label>
                                <textarea name="bio" class="form-control" rows="2">{{ old('bio') }}</textarea>
                                @error('bio')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tipo de Documento</label>
                                <input type="text" name="tipo_documento" class="form-control" value="{{ old('tipo_documento') }}">
                                @error('tipo_documento')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Número do Documento</label>
                                <input type="text" name="numero_documento" class="form-control" value="{{ old('numero_documento') }}">
                                @error('numero_documento')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Carteira móvel</label>
                                <select name="tipo_carteira" class="form-select">
                                    <option value="mpesa" @selected(old('tipo_carteira') === 'mpesa')>M-Pesa</option>
                                    <option value="mkesh" @selected(old('tipo_carteira') === 'mkesh')>mKesh</option>
                                    <option value="emola" @selected(old('tipo_carteira') === 'emola')>e-Mola</option>
                                    <option value="outro" @selected(old('tipo_carteira') === 'outro')>Outro</option>
                                </select>
                                @error('tipo_carteira')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Número da carteira</label>
                                <input type="text" name="numero_carteira" class="form-control" value="{{ old('numero_carteira') }}">
                                @error('numero_carteira')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-12 form-check">
                                <input type="checkbox" class="form-check-input" name="aceita_servicos_urgentes" value="1" {{ old('aceita_servicos_urgentes') ? 'checked' : '' }}>
                                <label class="form-check-label">Aceito ser notificado para serviços urgentes</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success w-100 mt-3">Criar conta de prestador</button>
                        <p class="text-muted small mt-2 mb-0">Após confirmar email, complete o perfil e aguarde verificação pelo admin.</p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
