@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="bg-success text-white rounded p-4 shadow-sm">
                <h1 class="h4 mb-2">Painel do Prestador</h1>
                <p class="mb-0">Veja pedidos compatíveis, acompanhe propostas e mantenha sua reputação em alta.</p>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">Próximos passos</h5>
                    <ul class="mb-0">
                        <li>Complete o perfil com documento e zonas de actuação.</li>
                        <li>Responda a pedidos com propostas rápidas e claras.</li>
                        <li>Mantenha disponibilidade e aceite urgências para subir no ranking.</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">Acesso rápido</h5>
                    <div class="d-grid gap-2">
                        <a href="{{ route('prestador.propostas.create', ['servico' => 1]) }}" class="btn btn-outline-primary">Responder a um serviço (exemplo)</a>
                        <a href="{{ route('prestador.propostas.show', ['servico' => 1, 'proposta' => 1]) }}" class="btn btn-outline-secondary">Ver uma proposta (exemplo)</a>
                    </div>
                    <small class="text-muted d-block mt-2">Os atalhos acima são ilustrativos; a listagem dinâmica surge via matching.</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
