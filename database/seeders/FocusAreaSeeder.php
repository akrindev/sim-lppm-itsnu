<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class FocusAreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $focusAreas = [
            'Keamanan',
            'Kesehatan',
            'Energi',
            'Maritim',
            'Pertahanan',
            'Manufaktur',
            'Keadilan Sosial',
            'Digitalisasi Industri',
            'Teknologi Informasi dan Komunikasi (ICT)',
            'Kebencanaan',
            'Kecerdasan Buatan (AI)',
        ];

        foreach ($focusAreas as $area) {
            \App\Models\FocusArea::firstOrCreate(['name' => $area]);
        }
    }
}
