<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ParametroSistema extends Model
{
    use HasFactory;

    protected $table = 'parametros_sistema';

    protected $fillable = ['chave', 'valor', 'descricao'];

    public static function valor(string $chave, $default = null)
    {
        return Cache::remember("parametro_{$chave}", 60, function () use ($chave, $default) {
            return static::query()->where('chave', $chave)->value('valor') ?? $default;
        });
    }
}
