<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Iniciar sesión', false);
        $response->assertSee('Volver al inicio', false);
        $response->assertSee('min-h-[100svh]', false);
        $response->assertSee('rounded-2xl bg-coffee-700', false);
        $response->assertSee('id="togglePassword"', false);
        $response->assertSee('login-enter', false);
        $response->assertSee('login-field', false);
        $response->assertSee('@keyframes loginRise', false);
        $response->assertSee('prefers-reduced-motion: reduce', false);
        $response->assertDontSee('id="cookieNotice"', false);
        $response->assertDontSee('-mb-12 opacity-30', false);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
