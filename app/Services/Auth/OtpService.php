<?php

namespace App\Services\Auth;

use App\Models\DeviceSession;
use App\Models\OtpToken;
use App\Models\User;
use App\Services\Notifications\SmsService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OtpService
{
    public function __construct(private SmsService $smsService)
    {
    }

    public function issue(User $user, string $deviceFingerprint, ?string $userAgent = null, ?string $ipAddress = null): OtpToken
    {
        $session = DeviceSession::updateOrCreate(
            ['user_id' => $user->id, 'device_fingerprint' => $deviceFingerprint],
            [
                'user_agent' => $userAgent,
                'ip_address' => $ipAddress,
                'last_used_at' => now(),
            ]
        );

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $token = OtpToken::create([
            'user_id' => $user->id,
            'device_session_id' => $session->id,
            'code_hash' => Hash::make($code),
            'expires_at' => CarbonImmutable::now()->addMinutes(10),
            'ip_address' => $ipAddress,
        ]);

        $this->smsService->sendOtp($user->phone, $code);

        return $token;
    }

    public function verify(User $user, string $code, string $deviceFingerprint): ?DeviceSession
    {
        $session = DeviceSession::where('user_id', $user->id)
            ->where('device_fingerprint', $deviceFingerprint)
            ->first();

        $token = OtpToken::where('user_id', $user->id)
            ->when($session, fn ($q) => $q->where('device_session_id', $session->id))
            ->orderByDesc('expires_at')
            ->first();

        if (! $token || $token->isExpired() || $token->isConsumed()) {
            return null;
        }

        if (! Hash::check($code, $token->code_hash)) {
            return null;
        }

        $token->forceFill(['consumed_at' => now()])->save();

        if ($session) {
            $session->forceFill([
                'trusted_at' => $session->trusted_at ?? now(),
                'last_used_at' => now(),
            ])->save();
        }

        return $session;
    }

    public function generateFingerprint(?string $rawFingerprint, ?string $userAgent, ?string $ip): string
    {
        return hash('sha256', ($rawFingerprint ?: Str::uuid()) . '|' . ($userAgent ?: '') . '|' . ($ip ?: ''));
    }
}
