<?php

namespace App\Services\Payments;

use App\Models\Cliente;
use App\Models\Pagamento;
use App\Models\Prestador;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Karson\MpesaPhpSdk\Mpesa;

class MpesaPaymentGateway implements PaymentGatewayInterface
{
    public function __construct(private Mpesa $client)
    {
    }

    public function iniciarPagamentoLead(Pagamento $pagamento): void
    {
        $msisdn = $this->obterMsisdnPrestador($pagamento);
        $this->iniciarCobranca($pagamento, $msisdn, 'Pagamento de lead');
    }

    public function iniciarPagamentoSubscricao(Pagamento $pagamento): void
    {
        $msisdn = $this->obterMsisdnPrestador($pagamento);
        $this->iniciarCobranca($pagamento, $msisdn, 'Pagamento de subscrição');
    }

    public function iniciarPagamentoReserva(Pagamento $pagamento): void
    {
        $msisdn = $this->obterMsisdnCliente($pagamento);
        $this->iniciarCobranca($pagamento, $msisdn, 'Pagamento de reserva');
    }

    public function transferirParaPrestador(Pagamento $pagamento): void
    {
        $msisdn = $this->obterMsisdnPrestador($pagamento);
        $this->transferir($pagamento, $msisdn, 'Liberação para prestador');
    }

    public function processarCallback(array $payload): void
    {
        $referencia = $this->extrairReferencia($payload);
        $status = Arr::get($payload, 'status');

        if (! $referencia) {
            Log::warning('Callback M-Pesa recebido sem referência interna', $payload);
            return;
        }

        $pagamento = Pagamento::where('referencia_externa', $referencia)->first();

        if (! $pagamento) {
            Log::warning('Callback M-Pesa sem pagamento correspondente', $payload);
            return;
        }

        $metadados = $pagamento->metadados ?? [];
        $metadados['callback'] = $payload;
        $pagamento->metadados = $metadados;

        if ($this->callbackComSucesso($payload, $status)) {
            $pagamento->estado_pagamento = 'confirmado';
        } elseif ($this->callbackComFalha($payload, $status)) {
            $pagamento->estado_pagamento = 'falhado';
        }

        $pagamento->save();
    }

    private function iniciarCobranca(Pagamento $pagamento, ?string $msisdn, string $descricao): void
    {
        if (! $msisdn) {
            throw new InvalidArgumentException('Não foi possível determinar o número de telefone para cobrança.');
        }

        $response = $this->client->c2b(
            amount: (float) $pagamento->valor,
            msisdn: $msisdn,
            reference: $this->gerarReferencia($pagamento),
            transDescription: $descricao,
        );

        $this->persistirResposta($pagamento, $response);
    }

    private function transferir(Pagamento $pagamento, ?string $msisdn, string $descricao): void
    {
        if (! $msisdn) {
            throw new InvalidArgumentException('Não foi possível determinar o número de telefone para transferência.');
        }

        $response = $this->client->b2c(
            amount: (float) $pagamento->valor,
            msisdn: $msisdn,
            reference: $this->gerarReferencia($pagamento),
            remarks: $descricao,
        );

        $this->persistirResposta($pagamento, $response);
    }

    private function persistirResposta(Pagamento $pagamento, array $response): void
    {
        $pagamento->estado_pagamento = 'pendente';
        $pagamento->referencia_externa = $response['conversationID']
            ?? $response['output_ConversationID']
            ?? $pagamento->referencia_externa
            ?? $this->gerarReferencia($pagamento);

        $metadados = $pagamento->metadados ?? [];
        $metadados['mpesa_request'] = $response;
        $metadados['internal_reference'] = $metadados['internal_reference'] ?? $pagamento->referencia_externa;
        $pagamento->metadados = $metadados;
        $pagamento->save();
    }

    private function gerarReferencia(Pagamento $pagamento): string
    {
        return $pagamento->referencia_externa ?: sprintf('pg-%s', $pagamento->id ?? uniqid());
    }

    private function obterMsisdnPrestador(Pagamento $pagamento): ?string
    {
        $metadados = $pagamento->metadados ?? [];
        if (! empty($metadados['msisdn'])) {
            return $metadados['msisdn'];
        }

        if ($pagamento->prestador_id) {
            $prestador = Prestador::find($pagamento->prestador_id);
            if ($prestador && $prestador->numero_carteira) {
                return $prestador->numero_carteira;
            }

            if ($prestador) {
                $user = User::find($prestador->user_id);
                return $user?->phone;
            }
        }

        return null;
    }

    private function obterMsisdnCliente(Pagamento $pagamento): ?string
    {
        $metadados = $pagamento->metadados ?? [];
        if (! empty($metadados['msisdn'])) {
            return $metadados['msisdn'];
        }

        if ($pagamento->cliente_id) {
            $cliente = Cliente::find($pagamento->cliente_id);
            if ($cliente) {
                $user = User::find($cliente->user_id);
                return $user?->phone;
            }
        }

        return null;
    }

    private function extrairReferencia(array $payload): ?string
    {
        return Arr::get($payload, 'internal_reference')
            ?? Arr::get($payload, 'ThirdPartyReference')
            ?? Arr::get($payload, 'conversationID')
            ?? Arr::get($payload, 'ConversationID')
            ?? Arr::get($payload, 'output_ConversationID');
    }

    private function callbackComSucesso(array $payload, ?string $status): bool
    {
        $codigo = Arr::get($payload, 'ResultCode') ?? Arr::get($payload, 'output_ResponseCode');

        return in_array($status, ['Success', 'Completed'], true)
            || in_array($codigo, ['0', 0, '00', 'INS-0'], true);
    }

    private function callbackComFalha(array $payload, ?string $status): bool
    {
        $codigo = Arr::get($payload, 'ResultCode') ?? Arr::get($payload, 'output_ResponseCode');

        return in_array($status, ['Failed', 'Declined', 'Cancelled'], true)
            || in_array($codigo, ['1', 1, 'INS-3', 'INS-7', 'INS-6', 'INS-2051'], true);
    }
}
