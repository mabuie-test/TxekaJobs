<?php

namespace App\Http\Controllers\Prestador;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePropostaRequest;
use App\Models\Prestador;
use App\Models\Proposta;
use App\Models\Servico;
use App\Services\Payments\LeadPaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PropostaController extends Controller
{
    public function __construct(private readonly LeadPaymentService $leadPaymentService)
    {
    }

    public function create(Servico $servico): View
    {
        $this->authorize('view', $servico);

        return view('prestador.propostas.create', compact('servico'));
    }

    public function store(StorePropostaRequest $request, Servico $servico): RedirectResponse
    {
        $prestador = $this->prestador();
        $this->authorize('propor', $servico);

        $leadPago = $this->leadPaymentService->cobrarOuDescontarLead($prestador, $servico);

        $proposta = new Proposta($request->validated());
        $proposta->servico_id = $servico->id;
        $proposta->prestador_id = $prestador->id;
        $proposta->estado_proposta = 'enviada';
        $proposta->lead_pago = $leadPago;
        $proposta->fonte_lead = $leadPago ? 'subscricao' : 'pagamento';
        $proposta->save();

        return redirect()
            ->route('prestador.propostas.show', [$servico, $proposta])
            ->with('status', 'Proposta enviada. O cliente será notificado.');
    }

    private function prestador(): Prestador
    {
        /** @var Prestador $prestador */
        $prestador = Auth::user()?->prestador;
        abort_if(!$prestador, 403, 'Perfil de prestador não encontrado.');

        return $prestador;
    }
}
