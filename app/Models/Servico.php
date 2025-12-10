<?php

namespace App\Models;

use App\Events\ServicoConcluido;
use App\Events\ServicoContratado;
use App\Events\ServicoCriado;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;

class Servico extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'prestador_id',
        'categoria_id',
        'zona_id',
        'titulo',
        'descricao',
        'cidade',
        'bairro_texto',
        'referencia_localizacao_texto',
        'latitude',
        'longitude',
        'orcamento_estimado_min',
        'orcamento_estimado_max',
        'urgencia',
        'origem',
        'estado',
        'data_contratacao',
        'data_inicio_execucao',
        'data_conclusao',
    ];

    protected $casts = [
        'data_contratacao' => 'datetime',
        'data_inicio_execucao' => 'datetime',
        'data_conclusao' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::created(function (Servico $servico) {
            Event::dispatch(new ServicoCriado($servico));
        });

        static::updated(function (Servico $servico) {
            if ($servico->isDirty('estado')) {
                if ($servico->estado === 'contratado') {
                    Event::dispatch(new ServicoContratado($servico));
                }
                if ($servico->estado === 'concluido') {
                    Event::dispatch(new ServicoConcluido($servico));
                }
            }
        });
    }

    public function prestador()
    {
        return $this->belongsTo(Prestador::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function zona()
    {
        return $this->belongsTo(Zona::class);
    }

    public function propostas()
    {
        return $this->hasMany(Proposta::class);
    }

    public function avaliacoes()
    {
        return $this->hasMany(Avaliacao::class);
    }
}
