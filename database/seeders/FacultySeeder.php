<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class FacultySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $itsnu = \App\Models\Institution::where('name', 'like', '%ITSNU%')->first();

        if ($itsnu) {
            $faculties = [
                ['name' => 'Fakultas Sains dan Teknologi', 'code' => 'SAINTEK'],
                ['name' => 'Fakultas Desain Kreatif dan Bisnis Digital', 'code' => 'DEKABITA'],
            ];

            foreach ($faculties as $faculty) {
                \App\Models\Faculty::create([
                    'institution_id' => $itsnu->id,
                    'name' => $faculty['name'],
                    'code' => $faculty['code'],
                ]);
            }
        }
    }
}
