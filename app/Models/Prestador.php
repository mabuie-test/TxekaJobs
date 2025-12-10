<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prestador extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bio',
        'documento_identificacao',
        'tipo_documento',
        'numero_documento',
        'tipo_carteira',
        'numero_carteira',
        'estado_verificacao',
        'data_verificacao',
        'total_servicos_concluidos',
        'total_servicos_cancelados',
        'total_litigios_procedentes',
        'rating_medio_cacheado',
        'esta_disponivel',
        'aceita_servicos_urgentes',
    ];

    protected $casts = [
        'data_verificacao' => 'datetime',
        'esta_disponivel' => 'boolean',
        'aceita_servicos_urgentes' => 'boolean',
    ];
}
