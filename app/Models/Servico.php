<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
