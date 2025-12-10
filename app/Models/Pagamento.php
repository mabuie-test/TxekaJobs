<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pagamento extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo_pagamento',
        'servico_id',
        'prestador_id',
        'cliente_id',
        'admin_id',
        'valor',
        'moeda',
        'referencia_externa',
        'canal_pagamento',
        'estado_pagamento',
        'direccao_logica',
        'metadados',
    ];

    protected $casts = [
        'metadados' => 'array',
    ];
}
