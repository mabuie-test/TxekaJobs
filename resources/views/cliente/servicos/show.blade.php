@extends('layouts.app')

@section('content')
<div class="container py-3">
    <h1 class="h4">{{ $servico->titulo }}</h1>
    <p class="text-muted mb-1">{{ $servico->cidade }} • {{ $servico->bairro_texto }}</p>
    <p class="mb-3">{{ $servico->descricao }}</p>
    <div class="alert alert-info">Estado actual: <strong class="text-uppercase">{{ $servico->estado }}</strong></div>

    <h2 class="h5 mt-4">Propostas recebidas</h2>
    <div class="list-group">
        @forelse($servico->propostas as $proposta)
            <div class="list-group-item">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-semibold">{{ $proposta->prestador->user->name ?? 'Prestador' }}</div>
                        <div class="text-muted small">Valor: {{ number_format($proposta->valor_proposto, 2) }} MZN</div>
                        <div class="text-muted small">Mensagem: {{ $proposta->mensagem }}</div>
                    </div>
                    <span class="badge bg-secondary text-uppercase">{{ $proposta->estado_proposta }}</span>
                </div>
            </div>
        @empty
            <p class="text-muted">Ainda não chegaram propostas. O matching está em execução.</p>
        @endforelse
    </div>
</div>
@endsection
