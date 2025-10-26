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
        // Get institutions and study programs
        $itsnu = \App\Models\Institution::where('name', 'like', '%ITSNU%')->first();
        $studyProgram = \App\Models\StudyProgram::first();

        // create users with roles
        $roles = Role::all();

        foreach ($roles as $role) {
            $user = \App\Models\User::factory()->create([
                'name' => str($role->name)->title() . ' User',
                'email' => str($role->name)->slug() . '@email.com',
            ]);

            $type = in_array($role->name, ['mahasiswa', 'student']) ? 'mahasiswa' : 'dosen';

            $user->identity()->create([
                'identity_id' => $type === 'dosen'
                    ? fake()->numerify('##########') // NIDN 10 digits
                    : fake()->numerify('################'), // NIM 16 digits
                'sinta_id' => $type === 'dosen' ? fake()->optional(0.7)->numerify('######') : null,
                'type' => $type,
                'institution_id' => $itsnu?->id ?? \App\Models\Institution::first()->id,
                'study_program_id' => $studyProgram?->id,
                'address' => 'Jl. Example No. ' . rand(1, 100),
                'birthdate' => now()->subYears(rand(20, 40))->toDateString(),
                'birthplace' => fake()->city(),
                'profile_picture' => null,
            ]);

            $user->assignRole($role->name);
        }
    }
}
