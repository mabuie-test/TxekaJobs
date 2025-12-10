<?php

namespace App\Services\Payments;

use App\Models\Pagamento;
use App\Models\ParametroSistema;
use App\Models\Prestador;
use App\Models\Servico;
use App\Models\SubscricaoPrestador;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class LeadPaymentService
{
    public function __construct(private readonly PaymentGatewayInterface $gateway)
    {
    }

    /**
     * Tenta usar um lead incluído na subscrição; se não houver, inicia pagamento avulso.
     * @return bool true quando lead foi cobrado de subscrição; false quando requer pagamento externo
     */
    public function cobrarOuDescontarLead(Prestador $prestador, Servico $servico): bool
    {
        $subscricao = $prestador->subscricaoAtiva;
        if ($subscricao && $this->consumirLead($subscricao)) {
            return true;
        }

        $pagamento = $this->criarPagamentoLead($prestador, $servico);
        $this->gateway->iniciarPagamentoLead($pagamento);

        return false;
    }

    private function consumirLead(SubscricaoPrestador $subscricao): bool
    {
        return (bool) DB::transaction(function () use ($subscricao) {
            $subscricao->refresh();
            if ($subscricao->leads_restantes <= 0) {
                return false;
            }

            $subscricao->decrement('leads_restantes');
            return true;
        });
    }

    private function criarPagamentoLead(Prestador $prestador, Servico $servico): Pagamento
    {
        $valorLead = ParametroSistema::valor('valor_lead_padrao', 150);
        $pagamento = new Pagamento([
            'tipo_pagamento' => 'lead',
            'prestador_id' => $prestador->id,
            'cliente_id' => $servico->cliente_id,
            'servico_id' => $servico->id,
            'valor' => $valorLead,
            'moeda' => 'MZN',
            'canal_pagamento' => 'mpesa',
            'estado_pagamento' => 'pendente',
            'direccao_logica' => 'cliente_para_plataforma',
        ]);

        if (!$pagamento->save()) {
            throw new RuntimeException('Não foi possível iniciar registo de pagamento de lead.');
        }

        return $pagamento;
    }
}
