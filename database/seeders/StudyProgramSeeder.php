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
        // based on FacultySeeder
        $saintek = \App\Models\Faculty::where('code', 'SAINTEK')->first();
        $dekabita = \App\Models\Faculty::where('code', 'DEKABITA')->first();

        // saintek study programs
        // - Informatika
        // - Teknologi Informasi
        // - Fisika
        // - Teknik Industri

        // dekabita study programs
        // - Kriya Batik
        // - Administrasi Perkantoran
        // - Akuntansi

        if ($saintek) {
            $saintekPrograms = [
                ['name' => 'Informatika', 'code' => 'IF'],
                ['name' => 'Teknologi Informasi', 'code' => 'TI'],
                ['name' => 'Fisika', 'code' => 'FIS'],
                ['name' => 'Teknik Industri', 'code' => 'TIU'],
            ];

            foreach ($saintekPrograms as $program) {
                \App\Models\StudyProgram::create([
                    'faculty_id' => $saintek->id,
                    'name' => $program['name'],
                    'code' => $program['code'],
                ]);
            }
        }

        if ($dekabita) {
            $dekabitaPrograms = [
                ['name' => 'Kriya Batik', 'code' => 'KB'],
                ['name' => 'Administrasi Perkantoran', 'code' => 'AP'],
                ['name' => 'Akuntansi', 'code' => 'AK'],
            ];

            foreach ($dekabitaPrograms as $program) {
                \App\Models\StudyProgram::create([
                    'faculty_id' => $dekabita->id,
                    'name' => $program['name'],
                    'code' => $program['code'],
                ]);
            }
        }
    }
}
