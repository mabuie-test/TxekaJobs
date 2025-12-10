<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Litigio extends Model
{
    use HasFactory;

    protected $fillable = [
        'servico_id',
        'aberto_por_user_id',
        'tipo_problema',
        'descricao',
        'estado_litigio',
        'decisao',
        'percentagem_reembolso_cliente',
        'percentagem_pagamento_prestador',
        'resolvido_por_admin_id',
    ];

    protected $casts = [
        'percentagem_reembolso_cliente' => 'float',
        'percentagem_pagamento_prestador' => 'float',
    ];

    public function servico()
    {
        return $this->belongsTo(Servico::class);
    }
}
