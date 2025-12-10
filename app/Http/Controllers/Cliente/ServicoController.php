<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServicoRequest;
use App\Models\Cliente;
use App\Models\Servico;
use App\Services\Servicos\ServicoStateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ServicoController extends Controller
{
    public function __construct(private readonly ServicoStateService $stateService)
    {
    }

    public function index(): View
    {
        $cliente = $this->cliente();
        $servicos = Servico::query()
            ->where('cliente_id', $cliente->id)
            ->latest()
            ->paginate(10);

        return view('cliente.servicos.index', compact('servicos'));
    }

    public function create(): View
    {
        return view('cliente.servicos.create');
    }

    public function store(StoreServicoRequest $request): RedirectResponse
    {
        $cliente = $this->cliente();

        $servico = new Servico($request->validated());
        $servico->cliente_id = $cliente->id;
        $servico->estado = 'aberto';
        $this->stateService->abrirServico($servico);

        return redirect()
            ->route('cliente.servicos.show', $servico)
            ->with('status', 'Pedido criado com sucesso. Estamos a encontrar prestadores adequados.');
    }

    public function show(Servico $servico): View
    {
        $this->authorize('view', $servico);

        $servico->load(['propostas.prestador.user', 'categoria', 'zona']);

        return view('cliente.servicos.show', compact('servico'));
    }

    private function cliente(): Cliente
    {
        /** @var Cliente $cliente */
        $cliente = Auth::user()?->cliente;
        abort_if(!$cliente, 403, 'Perfil de cliente não encontrado.');

        return $cliente;
    }
}
