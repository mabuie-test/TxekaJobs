<?php

namespace Tests\Feature\Payments;

use App\Services\Payments\PaymentGatewayInterface;
use Mockery;
use Tests\TestCase;

class MpesaCallbackControllerTest extends TestCase
{
    public function test_callback_payload_is_forwarded_to_gateway(): void
    {
        $gateway = Mockery::mock(PaymentGatewayInterface::class);
        $gateway->shouldReceive('processarCallback')
            ->once()
            ->with(['foo' => 'bar']);

        $this->app->instance(PaymentGatewayInterface::class, $gateway);

        $response = $this->postJson('/api/pagamentos/mpesa/callback', ['foo' => 'bar']);

        $response->assertOk()->assertJson(['status' => 'ok']);
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
