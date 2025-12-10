<?php

namespace App\Jobs;

use App\Models\Servico;
use App\Services\Notifications\SmsService;
use App\Services\Notifications\WhatsappService;
use App\Services\Servicos\PrestadorRankingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MatchPrestadoresJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Servico $servico)
    {
    }

    public function handle(
        PrestadorRankingService $rankingService,
        SmsService $smsService,
        WhatsappService $whatsappService
    ): void {
        $prestadores = $rankingService->selecionarPrestadoresParaServico($this->servico);

        foreach ($prestadores as $prestador) {
            $destino = $prestador->user?->phone ?? $prestador->numero_carteira;
            $conteudo = sprintf(
                'Novo pedido em %s (%s) - %s',
                $this->servico->cidade,
                $this->servico->bairro_texto,
                $this->servico->titulo
            );

            if ($destino) {
                $smsService->sendNotification($destino, $conteudo, [
                    'servico_id' => $this->servico->id,
                    'categoria_id' => $this->servico->categoria_id,
                ]);
                $whatsappService->sendNotification($destino, $conteudo, [
                    'servico_id' => $this->servico->id,
                ]);
            }
        }
    }
}
