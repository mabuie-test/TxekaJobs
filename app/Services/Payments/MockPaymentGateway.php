<?php

namespace App\Services\Payments;

use App\Models\Pagamento;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class MockPaymentGateway implements PaymentGatewayInterface
{
    public function iniciarPagamentoLead(Pagamento $pagamento): void
    {
        $this->confirmar($pagamento, 'Pagamento de lead simulado');
    }

    public function iniciarPagamentoSubscricao(Pagamento $pagamento): void
    {
        $this->confirmar($pagamento, 'Pagamento de subscrição simulado');
    }

    public function iniciarPagamentoReserva(Pagamento $pagamento): void
    {
        $this->confirmar($pagamento, 'Reserva inicial simulada');
    }

    public function transferirParaPrestador(Pagamento $pagamento): void
    {
        $this->confirmar($pagamento, 'Transferência simulada para prestador');
    }

    public function processarCallback(array $payload): void
    {
        Log::info('MockPaymentGateway callback recebido', $payload);
    }

    private function confirmar(Pagamento $pagamento, string $mensagem): void
    {
        $pagamento->estado_pagamento = 'confirmado';
        $metadados = $pagamento->metadados ?? [];
        $metadados['mock_confirmado_em'] = Carbon::now()->toIso8601String();
        $metadados['mensagem'] = $mensagem;
        $pagamento->metadados = $metadados;
        $pagamento->save();
    }
}
