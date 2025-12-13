<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proposta extends Model
{
    use HasFactory;

    protected $fillable = [
        'servico_id',
        'prestador_id',
        'valor_proposto',
        'mensagem',
        'tempo_estimado_execucao',
        'estado_proposta',
        'lead_pago',
        'fonte_lead',
    ];

    protected $casts = [
        'valor_proposto' => 'float',
        'lead_pago' => 'boolean',
    ];

    public function servico()
    {
        return $this->belongsTo(Servico::class);
    }
}
