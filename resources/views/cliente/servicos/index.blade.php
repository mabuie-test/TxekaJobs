@extends('layouts.app')

@section('content')
<div class="container py-3">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="h4 mb-0">Meus pedidos</h1>
        <a href="{{ route('cliente.servicos.create') }}" class="btn btn-primary btn-sm">Criar pedido</a>
    </div>
    <div class="list-group">
        @forelse($servicos as $servico)
            <a href="{{ route('cliente.servicos.show', $servico) }}" class="list-group-item list-group-item-action">
                <div class="d-flex w-100 justify-content-between">
                    <h5 class="mb-1">{{ $servico->titulo }}</h5>
                    <small class="text-muted text-uppercase">{{ $servico->estado }}</small>
                </div>
                <p class="mb-1 text-muted">{{ \Illuminate\Support\Str::limit($servico->descricao, 80) }}</p>
                <small class="text-muted">{{ $servico->cidade }} • {{ $servico->bairro_texto }}</small>
            </a>
        @empty
            <p class="text-muted">Ainda não há pedidos. Crie o primeiro para iniciar o matching automático.</p>
        @endforelse
    </div>
    <div class="mt-3">
        {{ $servicos->links() }}
    </div>
</div>
@endsection
