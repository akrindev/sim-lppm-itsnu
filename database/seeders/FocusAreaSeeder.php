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
            'Teknologi Informasi dan Komunikasi',
            'Energi Terbarukan',
            'Ketahanan Pangan',
            'Kesehatan dan Obat',
            'Transportasi',
            'Pertahanan dan Keamanan',
            'Material Maju',
            'Kemaritiman',
            'Kebencanaan',
            'Sosial Humaniora',
        ];

        foreach ($focusAreas as $area) {
            \App\Models\FocusArea::create(['name' => $area]);
        }
    }
}
