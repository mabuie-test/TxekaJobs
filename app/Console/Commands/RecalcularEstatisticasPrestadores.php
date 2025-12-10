<?php

namespace App\Console\Commands;

use App\Services\Servicos\EstatisticasPrestadorService;
use Illuminate\Console\Command;

class RecalcularEstatisticasPrestadores extends Command
{
    protected $signature = 'txeka:recalcular-estatisticas {--queued : Envia o recálculo para a fila em vez de executar inline}';

    protected $description = 'Recalcula estatísticas e reputação dos prestadores em lote';

    public function handle(EstatisticasPrestadorService $service): int
    {
        if ($this->option('queued')) {
            dispatch(new \App\Jobs\RecalcularEstatisticasPrestadoresJob());
            $this->info('Job enfileirado para recálculo.');
            return self::SUCCESS;
        }

        $this->info('A recalcular estatísticas de prestadores...');
        $service->recalcularTodos();
        $this->info('Concluído.');

        return self::SUCCESS;
    }
}
