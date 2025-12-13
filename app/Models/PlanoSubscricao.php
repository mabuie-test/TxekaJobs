<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanoSubscricao extends Model
{
    use HasFactory;

    protected $table = 'planos_subscricao';

    protected $fillable = [
        'nome',
        'slug',
        'descricao',
        'preco_mensal',
        'numero_leads_incluidos',
        'prioridade_ranking_inicial',
        'numero_maximo_propostas_simultaneas',
        'destaque',
        'inclui_selo_visual',
    ];

    protected $casts = [
        'preco_mensal' => 'float',
        'numero_leads_incluidos' => 'int',
        'numero_maximo_propostas_simultaneas' => 'int',
        'destaque' => 'boolean',
        'inclui_selo_visual' => 'boolean',
    ];
}
