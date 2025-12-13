@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="h5 mb-3">Verifique o seu email</h1>
    <p class="mb-3">Enviámos um link de confirmação para {{ auth()->user()->email ?? 'o seu email' }}. Se não recebeu, pode reenviar abaixo.</p>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success">Um novo link de verificação foi enviado.</div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}" class="mb-2">
        @csrf
        <button class="btn btn-primary w-100">Reenviar link</button>
    </form>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="btn btn-link w-100">Sair</button>
    </form>
</div>
@endsection
