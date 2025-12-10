<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'morada_principal',
        'cidade',
        'bairro_principal',
        'referencia_localizacao_texto',
        'latitude',
        'longitude',
        'prefere_sms',
        'prefere_whatsapp',
        'prefere_email',
    ];
}
