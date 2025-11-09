<?php

namespace Database\Seeders;

use App\Models\Faculty;
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
        $institution = $itsnu ?? \App\Models\Institution::first();
        $studyProgram = \App\Models\StudyProgram::first();
        
        // Get faculty or create default if doesn't exist
        $faculty = Faculty::inRandomOrder()->first();
        
        // If no faculty exists, create a default one
        if (!$faculty) {
            $faculty = \App\Models\Faculty::create([
                'institution_id' => $institution->id,
                'name' => 'Fakultas Teknik',
                'code' => 'FT',
            ]);
        }

        // create users with roles
        $roles = Role::all();

        foreach ($roles as $role) {
            $count = $role->name === 'dosen' ? 5 : 1;

            for ($i = 0; $i < $count; $i++) {
                $user = \App\Models\User::factory()->create([
                    'name' => str($role->name)->title() . ' User' . ($count > 1 ? ' ' . ($i + 1) : ''),
                    'email' => str($role->name)->slug() . ($count > 1 ? $i + 1 : '') . '@email.com',
                    'email_verified_at' => now(),
                ]);

                $type = in_array($role->name, ['mahasiswa', 'student']) ? 'mahasiswa' : 'dosen';

                $user->identity()->create([
                    'identity_id' => $type === 'dosen'
                        ? fake()->numerify('##########') // NIDN 10 digits
                        : fake()->numerify('################'), // NIM 16 digits
                    'sinta_id' => $type === 'dosen' ? fake()->optional(0.7)->numerify('####') : null,
                    'type' => $type,
                    'institution_id' => $institution->id,
                    'study_program_id' => $studyProgram?->id,
                    'faculty_id' => $faculty->id,
                    'address' => 'Jl. Example No. ' . rand(1, 100),
                    'birthdate' => now()->subYears(rand(20, 40))->toDateString(),
                    'birthplace' => fake()->city(),
                    'profile_picture' => null,
                ]);

                $user->assignRole($role->name);
            }
        }

        $this->command->info('Users seeded successfully!');
        $this->command->info('Total users created: ' . \App\Models\User::count());
        $this->command->info('Faculty used: ' . $faculty->name);
    }
}
