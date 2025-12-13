<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscricaoPrestador extends Model
{
    use HasFactory;

    protected $table = 'subscricao_prestador';

    protected $fillable = [
        'prestador_id',
        'plano_subscricao_id',
        'data_inicio',
        'data_fim',
        'estado_subscricao',
        'renovacao_automatica',
        'leads_restantes',
        'ultimo_pagamento_id',
    ];

    protected $casts = [
        'data_inicio' => 'datetime',
        'data_fim' => 'datetime',
        'renovacao_automatica' => 'boolean',
        'leads_restantes' => 'integer',
    ];

    public function plano()
    {
        return $this->belongsTo(PlanoSubscricao::class, 'plano_subscricao_id');
    }

    public function scopeAtiva($query)
    {
        return $query->where('estado_subscricao', 'activo')
            ->where(function ($q) {
                $q->whereNull('data_fim')->orWhere('data_fim', '>=', Carbon::now());
            });
    }
}
