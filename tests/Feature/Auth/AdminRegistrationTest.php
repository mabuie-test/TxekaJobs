<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_request_is_rejected_without_valid_token(): void
    {
        config(['app.admin_registration_token' => 'expected-token']);

        $response = $this->postJson('/api/admin/register', [
            'name' => 'Admin Example',
            'email' => 'admin@example.com',
            'phone' => '+258840000000',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseCount('users', 0);
    }

    public function test_admin_is_created_with_valid_token(): void
    {
        config(['app.admin_registration_token' => 'expected-token']);

        $response = $this->withHeader('Authorization', 'Bearer expected-token')
            ->postJson('/api/admin/register', [
                'name' => 'Admin Example',
                'email' => 'admin@example.com',
                'phone' => '+258840000001',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.tipo_perfil', 'admin');

        $this->assertDatabaseHas('users', [
            'email' => 'admin@example.com',
            'phone' => '+258840000001',
            'tipo_perfil' => 'admin',
            'status' => 'activo',
        ]);

        $this->assertTrue(User::where('email', 'admin@example.com')->exists());
    }
}
