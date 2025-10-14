<?php

use App\Livewire\Users\Create as UsersCreate;
use App\Livewire\Users\Edit as UsersEdit;
use App\Livewire\Users\Index as UsersIndex;
use App\Livewire\Users\Show as UsersShow;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    collect([
        'superadmin',
        'admin lppm',
        'dosen',
        'reviewer',
        'rektor',
    ])->each(fn (string $role) => Role::create(['name' => $role]));
});

test('users index route requires admin role', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $this->get(route('users.index'))->assertForbidden();
});

test('admin can view the users index page', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin lppm');

    $this->actingAs($admin);

    $this->get(route('users.index'))->assertOk();
});

test('admin can filter users by role and status', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin lppm');

    $dosen = User::factory()->create([
        'name' => 'Dosen Person',
        'email_verified_at' => now(),
    ]);
    $dosen->assignRole('dosen');

    $reviewer = User::factory()->create([
        'name' => 'Reviewer Person',
        'email_verified_at' => null,
    ]);
    $reviewer->assignRole('reviewer');

    $this->actingAs($admin);

    Livewire::test(UsersIndex::class)
        ->set('role', 'dosen')
        ->assertViewHas('users', function (LengthAwarePaginator $users) use ($dosen, $reviewer): bool {
            $ids = $users->getCollection()->pluck('id');

            return $ids->contains($dosen->id) && ! $ids->contains($reviewer->id);
        })
        ->set('role', 'all')
        ->set('status', 'unverified')
        ->assertViewHas('users', function (LengthAwarePaginator $users) use ($reviewer, $dosen): bool {
            $ids = $users->getCollection()->pluck('id');

            return $ids->contains($reviewer->id) && ! $ids->contains($dosen->id);
        });
});

test('search filters users by name and email', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin lppm');

    $target = User::factory()->create([
        'name' => 'Unique Target',
        'email' => 'unique.target@example.com',
    ]);
    $target->assignRole('reviewer');

    User::factory()->create(['name' => 'Another User'])->assignRole('dosen');

    $this->actingAs($admin);

    Livewire::test(UsersIndex::class)
        ->set('search', 'unique target')
        ->assertViewHas('users', function (LengthAwarePaginator $users) use ($target): bool {
            return $users->total() === 1 && $users->getCollection()->first()->is($target);
        })
        ->set('search', 'unique.target@example.com')
        ->assertViewHas('users', function (LengthAwarePaginator $users) use ($target): bool {
            return $users->total() === 1 && $users->getCollection()->first()->is($target);
        });
});

test('admin can update a user via the edit page', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin lppm');

    $user = User::factory()->create([
        'name' => 'Initial Name',
        'email' => 'initial@example.com',
        'email_verified_at' => null,
    ]);
    $user->assignRole('dosen');

    $this->actingAs($admin);

    Livewire::test(UsersEdit::class, ['user' => $user->id])
        ->set('name', 'Updated Name')
        ->set('email', 'updated@example.com')
        ->set('selectedRoles', ['reviewer'])
        ->set('emailVerified', true)
        ->call('save')
        ->assertHasNoErrors();

    $user->refresh();

    expect($user->name)->toEqual('Updated Name');
    expect($user->email)->toEqual('updated@example.com');
    expect($user->hasRole('reviewer'))->toBeTrue();
    expect($user->hasVerifiedEmail())->toBeTrue();
});

test('user detail page is accessible for admins', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin lppm');

    $user = User::factory()->create();
    $user->assignRole('dosen');

    $this->actingAs($admin);

    Livewire::test(UsersShow::class, ['user' => $user->id])
        ->assertViewHas('user', function ($viewUser) use ($user): bool {
            return $viewUser->is($user);
        });
});

test('admin can create a user with specific roles', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin lppm');

    $this->actingAs($admin);

    Livewire::test(UsersCreate::class)
        ->call('open')
        ->set('name', 'New Person')
        ->set('email', 'new.person@example.com')
        ->set('password', 'Password1!')
        ->set('password_confirmation', 'Password1!')
        ->set('selectedRoles', ['dosen', 'reviewer'])
        ->call('save')
        ->assertHasNoErrors()
        ->assertSet('show', false);

    $user = User::where('email', 'new.person@example.com')->first();

    expect($user)->not->toBeNull();
    expect($user->hasRole('dosen'))->toBeTrue();
    expect($user->hasRole('reviewer'))->toBeTrue();
});
