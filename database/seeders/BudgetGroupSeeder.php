<?php

namespace Database\Seeders;

use App\Models\BudgetGroup;
use Illuminate\Database\Seeder;

class BudgetGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Based on BIMA Kemdikbud RAB Components 2024-2025
     * Reference: Buku Panduan Penelitian dan Pengabdian kepada Masyarakat 2025
     * 
     * Budget Percentage Limits:
     * - Honor/Upah: Max 10%
     * - Teknologi & Inovasi (Bahan/Peralatan): Min 50%
     * - Pelatihan: Max 20%
     * - Perjalanan: Max 15%
     * - Lainnya: Max 5%
     */
    public function run(): void
    {
        $groups = [
            [
                'code' => 'HONOR',
                'name' => 'Upah dan Jasa (Honor)',
                'description' => 'Honorarium peneliti, asisten, operator, tenaga ahli, dan jasa pihak ketiga (Maksimal 10% dari total anggaran)',
            ],
            [
                'code' => 'TEKNOLOGI',
                'name' => 'Teknologi dan Inovasi',
                'description' => 'Bahan habis pakai, bahan penelitian, alat laboratorium, peralatan pendukung, dan pengembangan teknologi (Minimal 50% dari total anggaran)',
            ],
            [
                'code' => 'PELATIHAN',
                'name' => 'Biaya Pelatihan',
                'description' => 'Pelatihan, workshop, penyuluhan, atau capacity building dalam rangka program penelitian/pengabdian (Maksimal 20%)',
            ],
            [
                'code' => 'PERJALANAN',
                'name' => 'Biaya Perjalanan',
                'description' => 'Transportasi, akomodasi, dan konsumsi untuk pelaksanaan kegiatan di lokasi penelitian/pengabdian dan koordinasi dengan mitra (Maksimal 15%)',
            ],
            [
                'code' => 'LAINNYA',
                'name' => 'Biaya Lainnya',
                'description' => 'Publikasi hasil, seminar, pelaporan, dokumentasi, dan kebutuhan administrasi penunjang lainnya (Maksimal 5%)',
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
