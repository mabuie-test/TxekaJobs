<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function cliente_can_register_and_get_redirected_to_verification(): void
    {
        $response = $this->post('/registar/cliente', [
            'name' => 'Cliente Demo',
            'email' => 'cliente@example.com',
            'phone' => '840000001',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'cidade' => 'Maputo',
        ]);

        $response->assertRedirect(route('verification.notice'));
        $this->assertDatabaseHas('users', [
            'email' => 'cliente@example.com',
            'tipo_perfil' => 'cliente',
        ]);
    }

    /** @test */
    public function prestador_registration_sets_pending_verification_and_wallet(): void
    {
        $response = $this->post('/registar/prestador', [
            'name' => 'Prestador Demo',
            'email' => 'prestador@example.com',
            'phone' => '840000002',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'tipo_carteira' => 'mpesa',
            'numero_carteira' => '840000002',
        ]);

        $response->assertRedirect(route('verification.notice'));
        $user = User::where('email', 'prestador@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('prestador', $user->tipo_perfil);
        $this->assertEquals('pendente', $user->prestador->estado_verificacao);
        $this->assertEquals('mpesa', $user->prestador->tipo_carteira);
    }
}
