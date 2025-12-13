<?php

namespace App\Listeners;

use App\Events\ServicoConcluido;
use App\Services\Servicos\EstatisticasPrestadorService;

class ActualizarEstatisticasPrestadorListener
{
    public function __construct(private readonly EstatisticasPrestadorService $service)
    {
    }

    public function handle(ServicoConcluido $event): void
    {
        if ($event->servico->prestador_id) {
            $this->service->recalcularParaPrestador($event->servico->prestador);
        }
    }
}
