<?php

namespace Tests\Unit;

use App\Models\Servico;
use App\Services\Servicos\ServicoStateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ServicoStateServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_transicoes_basicas(): void
    {
        $servico = Servico::create([
            'cliente_id' => 1,
            'categoria_id' => 1,
            'titulo' => 'Teste',
            'descricao' => 'Servico de teste',
            'cidade' => 'Maputo',
            'estado' => 'aberto',
        ]);

        $service = new ServicoStateService();
        $service->marcarComoEmPropostas($servico);
        $this->assertEquals('em_propostas', $servico->estado);

        $service->contratarPrestador($servico, 2);
        $this->assertEquals('contratado', $servico->estado);

        $service->marcarChegadaPrestador($servico);
        $this->assertEquals('em_execucao', $servico->estado);

        $service->marcarConclusao($servico);
        $this->assertEquals('concluido', $servico->estado);
    }
}
