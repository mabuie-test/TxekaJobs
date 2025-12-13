<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'device_fingerprint',
        'user_agent',
        'ip_address',
        'trusted_at',
        'last_used_at',
    ];

    protected $casts = [
        'trusted_at' => 'datetime',
        'last_used_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function otpTokens()
    {
        return $this->hasMany(OtpToken::class);
    }
}
