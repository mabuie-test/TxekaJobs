<?php

namespace App\Services\Payments;

use App\Models\Pagamento;

interface PaymentGatewayInterface
{
    public function iniciarPagamentoLead(Pagamento $pagamento): void;

    public function iniciarPagamentoSubscricao(Pagamento $pagamento): void;

    public function iniciarPagamentoReserva(Pagamento $pagamento): void;

    public function transferirParaPrestador(Pagamento $pagamento): void;

    public function processarCallback(array $payload): void;
}
