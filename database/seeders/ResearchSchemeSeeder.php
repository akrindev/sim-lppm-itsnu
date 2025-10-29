<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ResearchSchemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schemes = [
            ['name' => 'Penelitian Dasar', 'strata' => 'Dasar'],
            ['name' => 'Penelitian Terapan', 'strata' => 'Terapan'],
            ['name' => 'Penelitian Pengembangan', 'strata' => 'Pengembangan'],
            ['name' => 'Penelitian Dosen Pemula', 'strata' => 'Dasar'],
            ['name' => 'Penelitian Kompetitif Nasional', 'strata' => 'Terapan'],
            ['name' => 'Penelitian Disertasi Doktor', 'strata' => 'Dasar'],
            ['name' => 'Penelitian Tesis Magister', 'strata' => 'Dasar'],
            ['name' => 'Penelitian Kolaborasi Indonesia', 'strata' => 'Pengembangan'],
        ];

        foreach ($schemes as $scheme) {
            \App\Models\ResearchScheme::create($scheme);
        }
    }
}
