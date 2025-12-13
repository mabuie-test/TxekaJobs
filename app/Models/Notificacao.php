<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notificacao extends Model
{
    use HasFactory;

    /**
     * Laravel pluraliza "notificacao" como "notificacaos" por padrão; aqui fixamos
     * explicitamente para corresponder à tabela criada na migração.
     */
    protected $table = 'notificacoes';

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
