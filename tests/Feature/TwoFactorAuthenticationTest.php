<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows two-factor challenge when session exists', function () {
    session(['login.id' => 1, 'login.remember' => false]);

    $response = $this->get('two-factor-challenge');

    $response->assertStatus(200);
});

it('redirects to login when no session exists', function () {
    $response = $this->get('two-factor-challenge');

    $response->assertRedirect(route('login'));
});

it('stores valid recovery code and logs user in', function () {
    $user = User::factory()->create();
    $user->forceFill([
        'two_factor_secret' => encrypt('JBSWY3DPEHPK3PXP'),
        'two_factor_recovery_codes' => encrypt(json_encode(['recovery-code-1', 'recovery-code-2'])),
    ])->save();

    session(['login.id' => $user->id, 'login.remember' => false]);

    $response = $this->post('two-factor-challenge', [
        'recovery_code' => 'recovery-code-1',
    ]);

    $response->assertStatus(302);
    $this->assertAuthenticatedAs($user);
});

it('stores valid recovery code and logs user in with remember me', function () {
    $user = User::factory()->create();
    $user->forceFill([
        'two_factor_secret' => encrypt('JBSWY3DPEHPK3PXP'),
        'two_factor_recovery_codes' => encrypt(json_encode(['recovery-code-1', 'recovery-code-2'])),
    ])->save();

    session(['login.id' => $user->id, 'login.remember' => true]);

    $response = $this->post('two-factor-challenge', [
        'recovery_code' => 'recovery-code-1',
    ]);

    $response->assertStatus(302);
    $this->assertAuthenticatedAs($user);
});

it('rejects invalid two-factor code', function () {
    $user = User::factory()->create();
    $user->forceFill([
        'two_factor_secret' => encrypt('JBSWY3DPEHPK3PXP'),
    ])->save();

    session(['login.id' => $user->id, 'login.remember' => false]);

    $response = $this->post('two-factor-challenge', [
        'code' => '999999',
    ]);

    $response->assertStatus(302);
    $response->assertSessionHasErrors(['code']);
    $this->assertGuest();
});

it('rejects invalid recovery code', function () {
    $user = User::factory()->create();
    $user->forceFill([
        'two_factor_secret' => encrypt('JBSWY3DPEHPK3PXP'),
        'two_factor_recovery_codes' => encrypt(json_encode(['recovery-code-1', 'recovery-code-2'])),
    ])->save();

    session(['login.id' => $user->id, 'login.remember' => false]);

    $response = $this->post('two-factor-challenge', [
        'recovery_code' => 'invalid-code',
    ]);

    $response->assertStatus(302);
    $response->assertSessionHasErrors(['code']);
    $this->assertGuest();
});

it('has the correct route names that match Login component expectations', function () {
    $routeCollection = app('router')->getRoutes();

    $twoFactorLoginRoute = $routeCollection->getByName('two-factor.login');
    $twoFactorStoreRoute = $routeCollection->getByName('two-factor.login.store');

    expect($twoFactorLoginRoute)->not->toBeNull('Route two-factor.login should exist');
    expect($twoFactorStoreRoute)->not->toBeNull('Route two-factor.login.store should exist');
    expect($twoFactorLoginRoute->uri())->toBe('two-factor-challenge');
    expect($twoFactorStoreRoute->uri())->toBe('two-factor-challenge');
});
