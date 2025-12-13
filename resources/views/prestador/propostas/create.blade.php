@extends('layouts.app')

@section('content')
<div class="container py-3">
    <h1 class="h5">Responder ao pedido: {{ $servico->titulo }}</h1>
    <form method="POST" action="{{ route('prestador.propostas.store', $servico) }}" class="mt-3">
        @csrf
        <div class="mb-3">
            <label class="form-label">Valor proposto (MZN)</label>
            <input type="number" name="valor_proposto" step="0.01" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Tempo estimado</label>
            <input type="text" name="tempo_estimado_execucao" class="form-control" placeholder="ex: 2 horas" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Mensagem para o cliente</label>
            <textarea name="mensagem" class="form-control" rows="3" required></textarea>
        </div>
        <button class="btn btn-success w-100">Submeter proposta</button>
    </form>
</div>
@endsection
