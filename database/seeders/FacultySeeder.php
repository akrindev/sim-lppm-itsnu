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
                ['name' => 'Fakultas Teknik', 'code' => 'FT'],
                ['name' => 'Fakultas Kesehatan', 'code' => 'FK'],
                ['name' => 'Fakultas Ekonomi dan Bisnis', 'code' => 'FEB'],
                ['name' => 'Fakultas Hukum', 'code' => 'FH'],
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
