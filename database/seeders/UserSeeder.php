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
                'nidn' => '000000' . rand(1000, 9999),
            ]);

            $user->assignRole($role->name);
        }
    }
}
