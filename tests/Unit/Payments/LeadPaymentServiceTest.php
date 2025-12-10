<?php

namespace Tests\Unit\Payments;

use App\Models\Pagamento;
use App\Models\Prestador;
use App\Models\Servico;
use App\Models\SubscricaoPrestador;
use App\Services\Payments\LeadPaymentService;
use App\Services\Payments\PaymentGatewayInterface;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;

class LeadPaymentServiceTest extends TestCase
{
    public function test_consume_lead_from_active_subscription(): void
    {
        $prestador = new Prestador(['id' => 10]);
        $subscricao = new SubscricaoPrestador(['leads_restantes' => 2]);
        $prestador->setRelation('subscricaoAtiva', $subscricao);

        $gateway = Mockery::mock(PaymentGatewayInterface::class);
        $gateway->shouldNotReceive('iniciarPagamentoLead');

        $service = new LeadPaymentService($gateway);
        $servico = new Servico(['id' => 20, 'cliente_id' => 1]);

        DB::shouldReceive('transaction')->andReturnUsing(fn($cb) => $cb());
        $consumido = $service->cobrarOuDescontarLead($prestador, $servico);

        $this->assertTrue($consumido);
        $this->assertSame(1, $subscricao->leads_restantes);
    }

    public function test_creates_payment_when_no_leads_left(): void
    {
        $prestador = new Prestador(['id' => 10]);
        $prestador->setRelation('subscricaoAtiva', null);
        $servico = new Servico(['id' => 20, 'cliente_id' => 1]);

        $gateway = Mockery::mock(PaymentGatewayInterface::class);
        $gateway->shouldReceive('iniciarPagamentoLead')->once()->with(Mockery::type(Pagamento::class));

        $service = new LeadPaymentService($gateway);

        $this->mock(Pagamento::class, function ($mock) {
            $mock->shouldReceive('save')->andReturnTrue();
        });

        $resultado = $service->cobrarOuDescontarLead($prestador, $servico);

        $this->assertFalse($resultado);
    }
}
