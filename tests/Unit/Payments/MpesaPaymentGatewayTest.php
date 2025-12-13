<?php

namespace Tests\Unit\Payments;

use App\Models\Pagamento;
use App\Services\Payments\MpesaPaymentGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Karson\MpesaPhpSdk\Mpesa;
use Mockery;
use Tests\TestCase;

class MpesaPaymentGatewayTest extends TestCase
{
    use RefreshDatabase;

    public function test_callback_marca_pagamento_confirmado_e_idempotente(): void
    {
        $pagamento = Pagamento::create([
            'tipo_pagamento' => 'lead',
            'valor' => 100,
            'estado_pagamento' => 'pendente',
            'metadados' => ['internal_reference' => 'conv-123'],
        ]);

        $gateway = new MpesaPaymentGateway(Mockery::mock(Mpesa::class));

        $payload = [
            'conversationID' => 'conv-123',
            'status' => 'Success',
            'ResultCode' => '0',
        ];

        $gateway->processarCallback($payload);

        $pagamento->refresh();
        $this->assertEquals('confirmado', $pagamento->estado_pagamento);
        $this->assertEquals('conv-123', $pagamento->referencia_externa);
        $this->assertEquals('Success', $pagamento->metadados['mpesa_status']);

        // Chamada repetida não deve reverter estado confirmado
        $gateway->processarCallback(array_merge($payload, ['status' => 'Failed', 'ResultCode' => 'INS-7']));
        $pagamento->refresh();
        $this->assertEquals('confirmado', $pagamento->estado_pagamento);
    }
}
