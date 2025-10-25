<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // create users with roles
        $roles = Role::all();

        foreach ($roles as $role) {
            $user = \App\Models\User::factory()->create([
                'name' => str($role->name)->title() . ' User',
                'email' => str($role->name)->slug() . '@email.com',
            ]);

            $user->identity()->create([
                'identity_id' => rand(10000, 99999),
                'address' => 'Jl. Example No. ' . rand(1, 100),
                'birthdate' => now()->subYears(rand(20, 40))->toDateString(),
                'birthplace' => fake()->city(),
                'profile_picture' => null,
            ]);

            $user->assignRole($role->name);
        }
    }
}
