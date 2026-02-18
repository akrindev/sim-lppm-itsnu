<?php

namespace Tests\Feature\Auth;

use App\Livewire\Auth\Login;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->withoutTwoFactor()->create();

        $response = Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'password')
            ->set('captcha', 'test-token')
            ->call('login');

        $response
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticated();
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $response = Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'wrong-password')
            ->set('captcha', 'test-token')
            ->call('login');

        $response->assertHasErrors('email');

        $this->assertGuest();
    }

    public function test_users_with_two_factor_data_authenticate_normally_when_feature_is_disabled(): void
    {
        $user = User::factory()->create();

        $user->forceFill([
            'two_factor_secret' => encrypt('test-secret'),
            'two_factor_recovery_codes' => encrypt(json_encode(['code1', 'code2'])),
            'two_factor_confirmed_at' => now(),
        ])->save();

        $response = Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'password')
            ->set('captcha', 'test-token')
            ->call('login');

        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticatedAs($user);
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->withoutMiddleware()->post('/logout');

        $response->assertRedirect('/');

        $this->assertGuest();
    }
}
