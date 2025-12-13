@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h1 class="h4 mb-3">Criar conta de Cliente</h1>
                    <p class="text-muted">Peça serviços, receba propostas e acompanhe tudo com segurança.</p>

                    <form method="POST" action="{{ route('register.cliente.store') }}" novalidate>
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Nome</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                            @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                            @error('email')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Telemóvel</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" required>
                            @error('phone')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                            @error('password')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirmar Password</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Cidade</label>
                            <select name="cidade" class="form-select" required>
                                <option value="">Selecione...</option>
                                @foreach(($cities ?? []) as $city)
                                    <option value="{{ $city }}" @selected(old('cidade') === $city)>{{ $city }}</option>
                                @endforeach
                            </select>
                            @error('cidade')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Bairro</label>
                            <input type="text" name="bairro_principal" class="form-control" value="{{ old('bairro_principal') }}">
                            @error('bairro_principal')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Referência</label>
                            <textarea name="referencia_localizacao_texto" class="form-control" rows="2">{{ old('referencia_localizacao_texto') }}</textarea>
                            @error('referencia_localizacao_texto')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Criar conta e confirmar email</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
