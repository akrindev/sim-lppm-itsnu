<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class StudyProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $itsnu = \App\Models\Institution::where('name', 'like', '%ITSNU%')->first();

        if ($itsnu) {
            $programs = [
                'Teknik Informatika',
                'Sistem Informasi',
                'Teknik Elektro',
                'Teknik Mesin',
                'Teknik Industri',
                'Farmasi',
                'Kesehatan Masyarakat',
            ];

            foreach ($programs as $program) {
                \App\Models\StudyProgram::create([
                    'institution_id' => $itsnu->id,
                    'name' => $program,
                ]);
            }
        }

        // Add programs for other institutions
        $otherInstitutions = \App\Models\Institution::where('name', 'not like', '%ITSNU%')->take(3)->get();

        foreach ($otherInstitutions as $institution) {
            \App\Models\StudyProgram::create([
                'institution_id' => $institution->id,
                'name' => fake()->randomElement(['Manajemen', 'Akuntansi', 'Hukum', 'Pendidikan Agama Islam']),
            ]);
        }
    }
}
