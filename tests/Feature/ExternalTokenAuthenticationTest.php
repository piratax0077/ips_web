<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ExternalTokenAuthenticationTest extends TestCase
{
    public function test_it_exchanges_a_valid_external_token_for_a_session(): void
    {
        Http::fake([
            '*/api/get-user' => Http::response(['user' => ['id' => 7, 'name' => 'Cliente IPS']], 200),
        ]);

        $response = $this->postJson('/auth/token/exchange', ['token' => 'valid-token']);

        $response->assertOk()->assertJson(['authenticated' => true]);
        $this->assertSame(7, session('external_auth.user.id'));
        $this->assertGreaterThan(now()->timestamp, session('external_auth.expires_at'));
    }

    public function test_it_rejects_an_invalid_external_token(): void
    {
        Http::fake(['*/api/get-user' => Http::response(['message' => 'Unauthenticated'], 401)]);

        $this->postJson('/auth/token/exchange', ['token' => 'invalid-token'])
            ->assertUnauthorized();
    }

    public function test_a_fresh_exchanged_session_can_open_user_data(): void
    {
        Http::fake([
            '*/api/get-user' => Http::response(['user' => ['id' => 7, 'name' => 'Cliente IPS']], 200),
        ]);

        $this->postJson('/auth/token/exchange', ['token' => 'valid-token'])->assertOk();

        $this->get('/mis_datos')->assertOk();
        $this->get('/solicitudes')->assertOk();
        $this->get('/carrito')->assertOk();
        $this->get('/mis_datos')->assertOk();
    }

    public function test_a_query_token_cannot_create_a_session(): void
    {
        Http::fake([
            '*/api/get-user' => Http::response(['user' => ['id' => 9, 'name' => 'Cliente']], 200),
        ]);

        $response = $this->get('/solicitudes?token=temporary-token&source=email');

        $response->assertRedirect('/login');
        $this->assertNull(session('external_auth.user'));
        Http::assertNothingSent();
    }

    public function test_an_expired_local_session_is_rejected(): void
    {
        $response = $this->withSession([
            'external_auth.user' => ['id' => 9, 'name' => 'Cliente'],
            'external_auth.expires_at' => now()->subMinute()->timestamp,
        ])->get('/solicitudes');

        $response->assertRedirect('/login');
        $this->assertNull(session('external_auth.user'));
    }
}
