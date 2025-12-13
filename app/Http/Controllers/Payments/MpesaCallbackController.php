<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Services\Payments\PaymentGatewayInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MpesaCallbackController extends Controller
{
    public function __construct(private readonly PaymentGatewayInterface $gateway)
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        // Forward the raw payload for idempotent processing inside the gateway
        $payload = $request->all();
        $this->gateway->processarCallback($payload);

        return response()->json(['status' => 'ok']);
    }
}
