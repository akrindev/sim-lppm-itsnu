<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StudyProgram>
 */
class StudyProgramFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $programs = [
            'Teknik Informatika',
            'Sistem Informasi',
            'Teknik Elektro',
            'Teknik Mesin',
            'Teknik Industri',
            'Manajemen',
            'Akuntansi',
            'Ekonomi Syariah',
            'Hukum',
            'Pendidikan Agama Islam',
            'Farmasi',
            'Kesehatan Masyarakat',
        ];

        return [
            'institution_id' => \App\Models\Institution::factory(),
            'name' => fake()->randomElement($programs),
        ];
    }
}
