<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notificacao extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'canal',
        'destino',
        'conteudo_resumido',
        'payload_json',
        'estado_envio',
        'tentativa_actual',
        'proxima_tentativa_em',
    ];

    protected $casts = [
        'payload_json' => 'array',
        'proxima_tentativa_em' => 'datetime',
    ];
}
