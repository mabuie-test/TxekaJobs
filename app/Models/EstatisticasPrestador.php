<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstatisticasPrestador extends Model
{
    use HasFactory;

    protected $table = 'estatisticas_prestadores';

    protected $fillable = [
        'prestador_id',
        'media_rating',
        'total_servicos',
        'total_servicos_ultimos_90_dias',
        'taxa_cancelamento',
        'total_litigios_procedentes',
        'ultimo_calculo_em',
    ];

    protected $casts = [
        'media_rating' => 'float',
        'taxa_cancelamento' => 'float',
        'ultimo_calculo_em' => 'datetime',
    ];

    public function prestador()
    {
        return $this->belongsTo(Prestador::class);
    }
}
