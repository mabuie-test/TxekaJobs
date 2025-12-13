<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Avaliacao extends Model
{
    use HasFactory;

    protected $table = 'avaliacoes';

    protected $fillable = [
        'servico_id',
        'cliente_id',
        'prestador_id',
        'rating',
        'comentario',
        'visivel_publico',
    ];

    protected $casts = [
        'visivel_publico' => 'boolean',
    ];

    public function prestador()
    {
        return $this->belongsTo(Prestador::class);
    }

    public function servico()
    {
        return $this->belongsTo(Servico::class);
    }
}
