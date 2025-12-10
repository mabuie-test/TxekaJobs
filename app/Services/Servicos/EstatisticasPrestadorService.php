<?php

namespace App\Services\Servicos;

use App\Models\Avaliacao;
use App\Models\EstatisticasPrestador;
use App\Models\Litigio;
use App\Models\Prestador;
use App\Models\Servico;
use Carbon\Carbon;

class EstatisticasPrestadorService
{
    public function recalcularParaPrestador(Prestador $prestador): EstatisticasPrestador
    {
        $estatisticas = EstatisticasPrestador::firstOrNew(['prestador_id' => $prestador->id]);

        $totalServicos = Servico::query()
            ->where('prestador_id', $prestador->id)
            ->whereIn('estado', ['concluido', 'encerrado'])
            ->count();

        $totalUltimos90Dias = Servico::query()
            ->where('prestador_id', $prestador->id)
            ->where('created_at', '>=', Carbon::now()->subDays(90))
            ->count();

        $cancelados = Servico::query()
            ->where('prestador_id', $prestador->id)
            ->where('estado', 'encerrado')
            ->count();

        $ratingMedio = Avaliacao::query()
            ->where('prestador_id', $prestador->id)
            ->avg('rating');

        $litigiosProcedentes = Litigio::query()
            ->whereHas('servico', function ($query) use ($prestador) {
                $query->where('prestador_id', $prestador->id);
            })
            ->where('estado_litigio', 'resolvido')
            ->where(function ($query) {
                $query->where('percentagem_reembolso_cliente', '>', 0)
                    ->orWhere('percentagem_pagamento_prestador', '<', 100);
            })
            ->count();

        $estatisticas->fill([
            'media_rating' => round($ratingMedio ?? 0, 2),
            'total_servicos' => $totalServicos,
            'total_servicos_ultimos_90_dias' => $totalUltimos90Dias,
            'taxa_cancelamento' => $totalServicos > 0 ? round(($cancelados / $totalServicos) * 100, 2) : 0,
            'total_litigios_procedentes' => $litigiosProcedentes,
            'ultimo_calculo_em' => Carbon::now(),
        ]);

        $estatisticas->save();

        return $estatisticas;
    }

    public function recalcularTodos(): void
    {
        Prestador::query()->chunk(200, function ($prestadores) {
            foreach ($prestadores as $prestador) {
                $this->recalcularParaPrestador($prestador);
            }
        });
    }
}
