<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // there are 8 roles
        $roles = [
            'superadmin', // for it admin / developer only
            'admin lppm',
            'admin lppm saintek',
            'admin lppm dekabita',
            'kepala lppm',
            'dosen',
            'reviewer',
            'rektor',
            'dekan',
        ];

        foreach ($roles as $role) {
            Role::create(['name' => $role]);
        }
    }
}
