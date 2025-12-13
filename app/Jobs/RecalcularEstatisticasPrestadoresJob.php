<?php

namespace App\Jobs;

use App\Services\Servicos\EstatisticasPrestadorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RecalcularEstatisticasPrestadoresJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
    }

    public function handle(EstatisticasPrestadorService $service): void
    {
        $service->recalcularTodos();
    }
}
