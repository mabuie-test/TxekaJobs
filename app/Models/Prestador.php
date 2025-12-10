<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prestador extends Model
{
    use HasFactory;

    protected $table = 'prestadores';

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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categorias()
    {
        return $this->belongsToMany(Categoria::class, 'prestador_categoria');
    }

    public function zonas()
    {
        return $this->belongsToMany(Zona::class, 'prestador_zona');
    }

    public function estatisticas()
    {
        return $this->hasOne(EstatisticasPrestador::class);
    }

    public function subscricoes()
    {
        return $this->hasMany(SubscricaoPrestador::class);
    }

    public function subscricaoAtiva()
    {
        return $this->hasOne(SubscricaoPrestador::class)->ativa()->latestOfMany('data_inicio');
    }

    public function avaliacoes()
    {
        return $this->hasMany(Avaliacao::class);
    }
}
