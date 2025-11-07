<?php

namespace Database\Seeders;

use App\Models\BudgetGroup;
use Illuminate\Database\Seeder;

class BudgetGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groups = [
            [
                'code' => 'HON',
                'name' => 'Honorarium',
                'description' => 'Biaya honorarium pelaksana penelitian dan narasumber',
            ],
            [
                'code' => 'PER',
                'name' => 'Peralatan Penunjang',
                'description' => 'Biaya pengadaan peralatan penelitian yang menunjang kegiatan',
            ],
            [
                'code' => 'BHP',
                'name' => 'Bahan Habis Pakai',
                'description' => 'Biaya bahan dan material yang habis terpakai dalam penelitian',
            ],
            [
                'code' => 'PRJ',
                'name' => 'Perjalanan',
                'description' => 'Biaya perjalanan untuk survei, pengumpulan data, dan kegiatan lapangan',
            ],
            [
                'code' => 'LAN',
                'name' => 'Lain-lain',
                'description' => 'Biaya publikasi, seminar, pelaporan, dan kegiatan penunjang lainnya',
            ],
        ];

        foreach ($groups as $group) {
            BudgetGroup::updateOrCreate(
                ['code' => $group['code']],
                $group
            );
        }
    }
}
