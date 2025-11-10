<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ResearchSchemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Based on BIMA Kemdikbud 2024-2025
     * Reference: Buku Panduan Penelitian dan Pengabdian kepada Masyarakat 2025
     */
    public function run(): void
    {
        $schemes = [
            // PENELITIAN DASAR
            [
                'name' => 'Penelitian Dosen Pemula (PDP)',
                'strata' => 'Dasar',
                'description' => 'Untuk dosen pemula dengan pengalaman riset terbatas',
            ],
            [
                'name' => 'Penelitian Dosen Pemula Afirmasi (PDP Afirmasi)',
                'strata' => 'Dasar',
                'description' => 'PDP dengan afirmasi untuk daerah tertentu',
            ],
            [
                'name' => 'Penelitian Pascasarjana (PPS)',
                'strata' => 'Dasar',
                'description' => 'Penelitian untuk mahasiswa S2/S3 dengan dosen pembimbing',
            ],
            [
                'name' => 'Penelitian Fundamental (PF)',
                'strata' => 'Dasar',
                'description' => 'Penelitian fundamental tingkat lanjut',
            ],
            [
                'name' => 'Program Magister Menuju Dokter Sarjana Unggul (PMDSU)',
                'strata' => 'Dasar',
                'description' => 'Program fast-track S2 ke S3',
            ],
            [
                'name' => 'Penelitian Kerja Sama Dalam Negeri (PKDN)',
                'strata' => 'Dasar',
                'description' => 'Kolaborasi antar PT dalam negeri',
            ],
            [
                'name' => 'Kolaborasi Penelitian Strategis (KATALIS)',
                'strata' => 'Dasar',
                'description' => 'Kolaborasi strategis lintas klaster',
            ],

            // PENELITIAN TERAPAN
            [
                'name' => 'Penelitian Terapan (PT)',
                'strata' => 'Terapan',
                'description' => 'Penelitian dengan aplikasi praktis',
            ],

            // PENGABDIAN KEPADA MASYARAKAT
            [
                'name' => 'Pemberdayaan Wilayah (PW)',
                'strata' => 'PKM',
                'description' => 'Pemberdayaan masyarakat berbasis wilayah',
            ],
            [
                'name' => 'Pemberdayaan Desa Binaan (PDB)',
                'strata' => 'PKM',
                'description' => 'Pemberdayaan desa binaan perguruan tinggi',
            ],

            // INTERNAL SCHEMES (Optional - for institutional use)
            [
                'name' => 'Penelitian Internal ITSNU',
                'strata' => 'Dasar',
                'description' => 'Penelitian yang didanai oleh ITSNU',
            ],
            [
                'name' => 'Pengabdian Internal ITSNU',
                'strata' => 'PKM',
                'description' => 'Pengabdian yang didanai oleh ITSNU',
            ],
        ];

        foreach ($schemes as $scheme) {
            \App\Models\ResearchScheme::updateOrCreate(
                ['name' => $scheme['name']],
                $scheme
            );
        }
    }
}
