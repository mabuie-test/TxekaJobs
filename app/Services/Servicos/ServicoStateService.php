<?php

namespace App\Services\Servicos;

use App\Models\Servico;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ServicoStateService
{
    public function abrirServico(Servico $servico): Servico
    {
        return $this->transition($servico, 'aberto');
    }

    public function marcarComoEmPropostas(Servico $servico): Servico
    {
        return $this->transition($servico, 'em_propostas');
    }

    public function contratarPrestador(Servico $servico, int $prestadorId): Servico
    {
        return DB::transaction(function () use ($servico, $prestadorId) {
            if (!in_array($servico->estado, ['em_propostas', 'aberto'])) {
                throw new InvalidArgumentException('Estado actual não permite contratação.');
            }
            $servico->prestador_id = $prestadorId;
            $servico->data_contratacao = Carbon::now();
            return $this->transition($servico, 'contratado');
        });
    }

    public function marcarChegadaPrestador(Servico $servico): Servico
    {
        return $this->ensureState($servico, ['contratado'], 'em_execucao', 'data_inicio_execucao');
    }

    public function marcarConclusao(Servico $servico): Servico
    {
        return $this->ensureState($servico, ['em_execucao'], 'concluido', 'data_conclusao');
    }

    public function abrirLitigio(Servico $servico): Servico
    {
        return $this->transition($servico, 'em_litigio');
    }

    public function encerrarServico(Servico $servico): Servico
    {
        return $this->transition($servico, 'encerrado');
    }

    private function ensureState(Servico $servico, array $permitidos, string $novoEstado, ?string $timestampField = null): Servico
    {
        if (!in_array($servico->estado, $permitidos)) {
            throw new InvalidArgumentException('Transição de estado inválida.');
        }

        if ($timestampField) {
            $servico->{$timestampField} = Carbon::now();
        }

        return $this->transition($servico, $novoEstado);
    }

    private function transition(Servico $servico, string $estado): Servico
    {
        $servico->estado = $estado;
        $servico->save();
        return $servico;
    }
}
