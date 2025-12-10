<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'tipo_perfil',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
    ];

    public function deviceSessions()
    {
        return $this->hasMany(DeviceSession::class);
    }

    public function otpTokens()
    {
        return $this->hasMany(OtpToken::class);
    }

    public function cliente()
    {
        return $this->hasOne(Cliente::class);
    }

    public function prestador()
    {
        return $this->hasOne(Prestador::class);
    }
}
