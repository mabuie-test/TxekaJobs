<?php

namespace App\Services\Notifications;

use App\Models\Notificacao;

class WhatsappService
{
    public function sendOtp(string $destino, string $codigo): Notificacao
    {
        return $this->store('whatsapp', $destino, "OTP: {$codigo}", ['tipo' => 'otp']);
    }

    public function sendNotification(string $destino, string $conteudo, array $payload = []): Notificacao
    {
        return $this->store('whatsapp', $destino, $conteudo, $payload);
    }

    public function sendServiceStatusUpdate(string $destino, string $estado, array $payload = []): Notificacao
    {
        return $this->store('whatsapp', $destino, "Estado do serviço: {$estado}", $payload);
    }

    private function store(string $canal, string $destino, string $conteudo, array $payload): Notificacao
    {
        return Notificacao::create([
            'canal' => $canal,
            'destino' => $destino,
            'conteudo_resumido' => $conteudo,
            'payload_json' => $payload,
            'estado_envio' => 'pendente',
        ]);
    }
}
