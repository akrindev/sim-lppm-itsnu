<?php

declare(strict_types=1);


use App\Models\User;
use App\Models\Identity;

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class);
uses(RefreshDatabase::class);

it('user has one identity', function () {
    $user = User::factory()->create();
    $identity = Identity::factory()->create(['user_id' => $user->id]);
    expect($user->identity)->not->toBeNull();
    expect($user->identity->id)->toBe($identity->id);
});

it('identity belongs to user', function () {
    $user = User::factory()->create();
    $identity = Identity::factory()->create(['user_id' => $user->id]);
    expect($identity->user)->not->toBeNull();
    expect($identity->user->id)->toBe($user->id);
});
