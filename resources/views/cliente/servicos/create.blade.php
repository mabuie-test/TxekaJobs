@extends('layouts.app')

@section('content')
<div class="container py-3">
    <h1 class="h4 mb-3">Novo pedido de serviço</h1>
    <form method="POST" action="{{ route('cliente.servicos.store') }}" class="needs-validation" novalidate>
        @csrf
        <div class="mb-3">
            <label class="form-label">Título</label>
            <input type="text" name="titulo" class="form-control" value="{{ old('titulo') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Categoria</label>
            <input type="number" name="categoria_id" class="form-control" value="{{ old('categoria_id') }}" required>
            <small class="text-muted">Escolha o ID de categoria (placeholder enquanto não há selector dinâmico).</small>
        </div>
        <div class="row g-2 mb-3">
            <div class="col">
                <label class="form-label">Cidade</label>
                <input type="text" name="cidade" class="form-control" value="{{ old('cidade') }}" required>
            </div>
            <div class="col">
                <label class="form-label">Bairro</label>
                <input type="text" name="bairro_texto" class="form-control" value="{{ old('bairro_texto') }}" required>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Descrição detalhada</label>
            <textarea name="descricao" class="form-control" rows="4" required>{{ old('descricao') }}</textarea>
        </div>
        <div class="row g-2 mb-3">
            <div class="col">
                <label class="form-label">Orçamento mínimo (MZN)</label>
                <input type="number" step="0.01" name="orcamento_estimado_min" class="form-control" value="{{ old('orcamento_estimado_min') }}">
            </div>
            <div class="col">
                <label class="form-label">Orçamento máximo (MZN)</label>
                <input type="number" step="0.01" name="orcamento_estimado_max" class="form-control" value="{{ old('orcamento_estimado_max') }}">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Urgência</label>
            <select name="urgencia" class="form-select" required>
                <option value="agora">Agora</option>
                <option value="hoje">Hoje</option>
                <option value="esta_semana">Esta semana</option>
            </select>
        </div>
        <button class="btn btn-primary w-100">Submeter pedido</button>
    </form>
</div>
@endsection
