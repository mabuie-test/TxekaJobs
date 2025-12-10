<?php

namespace App\Services\Servicos;

use App\Models\Prestador;
use App\Models\Servico;
use Illuminate\Support\Collection;

class PrestadorRankingService
{
    public function calcularScore(Prestador $prestador, ?Servico $servico = null): float
    {
        $score = 0;
        $stats = $prestador->estatisticas;
        if ($stats) {
            $score += ($stats->media_rating ?? 0) * 20; // valoriza ratings até 100 pontos
            $score += min($stats->total_servicos, 100) * 0.3; // histórico de serviços
            $score -= ($stats->taxa_cancelamento ?? 0) * 0.5; // penaliza cancelamentos
            $score -= ($stats->total_litigios_procedentes ?? 0) * 1.5;
        }

        if ($prestador->estado_verificacao === 'verificado') {
            $score += 15;
        } elseif ($prestador->estado_verificacao === 'pendente') {
            $score += 5;
        } else {
            $score -= 10;
        }

        if ($prestador->esta_disponivel) {
            $score += 5;
        }

        if ($servico && $servico->urgencia === 'agora') {
            $score += $prestador->aceita_servicos_urgentes ? 10 : -10;
        }

        $subscricaoAtiva = $prestador->subscricaoAtiva()->with('plano')->first();
        if ($subscricaoAtiva && $subscricaoAtiva->plano) {
            $score += $subscricaoAtiva->plano->prioridade_ranking_inicial ?? 0;
            if ($subscricaoAtiva->plano->destaque) {
                $score += 5;
            }
        }

        return round($score, 2);
    }

    public function selecionarPrestadoresParaServico(Servico $servico, int $limite = 25): Collection
    {
        $candidatos = Prestador::query()
            ->with(['estatisticas', 'subscricaoAtiva.plano'])
            ->whereHas('categorias', function ($query) use ($servico) {
                $query->where('categorias.id', $servico->categoria_id);
            })
            ->when($servico->zona_id, function ($query) use ($servico) {
                $query->whereHas('zonas', function ($zonaQuery) use ($servico) {
                    $zonaQuery->where('zonas.id', $servico->zona_id);
                });
            })
            ->where(function ($query) {
                $query->whereNull('esta_disponivel')->orWhere('esta_disponivel', true);
            })
            ->get();

        return $candidatos->sortByDesc(function (Prestador $prestador) use ($servico) {
            return $this->calcularScore($prestador, $servico);
        })->take($limite)->values();
    }
}
