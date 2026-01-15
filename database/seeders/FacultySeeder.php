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
        $institution = \App\Models\Institution::where('name', 'like', '%Institut Teknologi dan Sains Nahdlatul Ulama%')->first()
            ?? \App\Models\Institution::first();

        if ($institution) {
            $faculties = [
                ['name' => 'Fakultas Sains dan Teknologi', 'code' => 'SAINTEK'],
                ['name' => 'Fakultas Desain Kreatif dan Bisnis Digital', 'code' => 'DEKABITA'],
            ];

            foreach ($faculties as $faculty) {
                \App\Models\Faculty::updateOrCreate(
                    ['code' => $faculty['code']],
                    [
                        'institution_id' => $institution->id,
                        'name' => $faculty['name'],
                        'code' => $faculty['code'],
                    ]
                );
            }
        }
    }
}
