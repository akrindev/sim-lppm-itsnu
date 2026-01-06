<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class NationalPrioritySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Based on Prioritas Riset Nasional (PRN) 2024-2025
     * Reference: Perpres No. 38 Tahun 2018 - Rencana Induk Riset Nasional 2017-2045
     * Total: 49 specific innovation research agendas under 9 focus areas
     */
    public function run(): void
    {
        $priorities = [
            [
                'name' => 'Keamanan',
                'description' => 'Sektor strategis untuk keamanan nasional dan ketertiban masyarakat',
            ],
            [
                'name' => 'Kesehatan',
                'description' => 'Sektor strategis untuk kemandirian obat, vaksin, dan alat kesehatan',
            ],
            [
                'name' => 'Energi',
                'description' => 'Sektor strategis untuk kemandirian energi dan energi baru terbarukan',
            ],
            [
                'name' => 'Maritim',
                'description' => 'Sektor strategis untuk ekonomi biru dan kedaulatan laut',
            ],
            [
                'name' => 'Pertahanan',
                'description' => 'Sektor strategis untuk kedaulatan wilayah dan alutsista',
            ],
            [
                'name' => 'Manufaktur',
                'description' => 'Sektor strategis untuk industri pengolahan dan hilirisasi',
            ],
            [
                'name' => 'Keadilan Sosial',
                'description' => 'Sektor strategis untuk kesejahteraan masyarakat dan pemerataan pembangunan',
            ],
            [
                'name' => 'Digitalisasi Industri',
                'description' => 'Sektor strategis untuk transformasi digital, AI, dan Industri 4.0',
            ],
        ];

        foreach ($priorities as $priority) {
            \App\Models\NationalPriority::updateOrCreate(
                ['name' => $priority['name']],
                $priority
            );
        }
    }
}
