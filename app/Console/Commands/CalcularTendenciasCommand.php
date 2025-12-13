<?php

namespace App\Console\Commands;

use App\Services\Insights\UsageLearningService;
use Illuminate\Console\Command;

class CalcularTendenciasCommand extends Command
{
    protected $signature = 'txeka:tendencias';
    protected $description = 'Recalcula tendências de categorias com base nos últimos 90 dias';

    public function handle(UsageLearningService $service): int
    {
        $resultados = $service->calcularTendencias();
        $this->info('Tendências actualizadas:');
        foreach ($resultados as $linha) {
            $this->line('- '.$linha['categoria'].' (score '.$linha['score'].')');
        }

        return self::SUCCESS;
    }
}
