<?php

namespace App\Services\Insights;

use App\Models\Categoria;
use App\Models\ParametroSistema;
use App\Models\Proposta;
use App\Models\Servico;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class UsageLearningService
{
    /**
     * Gera insights simples ("aprendizado") com base na frequência de serviços
     * e propostas recentes para sugerir categorias em alta.
     */
    public function calcularTendencias(): array
    {
        $limite = Carbon::now()->subDays(90);

        $servicosPorCategoria = Servico::where('created_at', '>=', $limite)
            ->selectRaw('categoria_id, count(*) as total')
            ->groupBy('categoria_id')
            ->pluck('total', 'categoria_id');

        $propostasPorCategoria = Proposta::where('created_at', '>=', $limite)
            ->selectRaw('servico_id, count(*) as total')
            ->groupBy('servico_id')
            ->pluck('total', 'servico_id');

        $categoriaScores = new Collection();

        foreach ($servicosPorCategoria as $categoriaId => $totalServicos) {
            $scoreServicos = $totalServicos;
            $scorePropostas = $this->propostasParaCategoria($propostasPorCategoria, $categoriaId);
            $categoriaScores[$categoriaId] = $scoreServicos + ($scorePropostas * 0.6);
        }

        $ranked = $categoriaScores
            ->sortDesc()
            ->take(5)
            ->map(function ($score, $categoriaId) {
                return [
                    'categoria' => Categoria::find($categoriaId)?->nome,
                    'score' => $score,
                ];
            })
            ->filter(fn ($item) => ! empty($item['categoria']))
            ->values()
            ->all();

        ParametroSistema::updateOrCreate(
            ['chave' => 'tendencias'],
            ['valor' => $ranked]
        );

        return $ranked;
    }

    private function propostasParaCategoria(Collection $propostasPorServico, int $categoriaId): int
    {
        $servicosIds = Servico::where('categoria_id', $categoriaId)->pluck('id');

        return $propostasPorServico
            ->only($servicosIds)
            ->sum();
    }
}
