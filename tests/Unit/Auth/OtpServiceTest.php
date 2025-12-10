<?php

namespace Tests\Unit\Auth;

use App\Models\User;
use App\Services\Auth\OtpService;
use App\Services\Notifications\SmsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OtpServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_issue_creates_token_and_notification(): void
    {
        $user = User::create([
            'name' => 'Cliente',
            'email' => 'cliente@example.com',
            'phone' => '258840000000',
            'password' => Hash::make('password'),
            'tipo_perfil' => 'cliente',
            'status' => 'activo',
        ]);

        $service = new OtpService(new SmsService());
        $service->issue($user, 'fingerprint-123', 'UnitTest', '127.0.0.1');

        $this->assertDatabaseHas('device_sessions', [
            'user_id' => $user->id,
            'device_fingerprint' => 'fingerprint-123',
        ]);

        $this->assertDatabaseHas('otp_tokens', [
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('notificacoes', [
            'destino' => $user->phone,
            'canal' => 'sms',
        ]);
    }

    public function test_verify_consumes_code_and_trusts_session(): void
    {
        $user = User::create([
            'name' => 'Prestador',
            'email' => 'prestador@example.com',
            'phone' => '258850000000',
            'password' => Hash::make('password'),
            'tipo_perfil' => 'prestador',
            'status' => 'activo',
        ]);

        $service = new OtpService(new SmsService());
        $token = $service->issue($user, 'fingerprint-verify', 'UnitTest', '127.0.0.1');
        $token->update(['code_hash' => Hash::make('123456')]);

        $session = $service->verify($user, '123456', 'fingerprint-verify');

        $this->assertNotNull($session);
        $token->refresh();
        $this->assertNotNull($token->consumed_at);
        $this->assertNotNull($session->trusted_at);
    }
}
