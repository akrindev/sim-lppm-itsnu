<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class NationalPrioritySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $priorities = [
            'Pangan',
            'Energi',
            'Kesehatan',
            'Lingkungan',
            'Material Maju',
            'IT dan Komunikasi',
            'Pertahanan dan Keamanan',
            'Transportasi',
            'Kemaritiman',
        ];

        foreach ($priorities as $priority) {
            \App\Models\NationalPriority::create(['name' => $priority]);
        }
    }
}
