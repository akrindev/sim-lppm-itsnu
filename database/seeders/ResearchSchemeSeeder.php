<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ResearchSchemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Based on BIMA Kemdiktisaintek 2026
     * Reference: Buku Panduan Penelitian dan Pengabdian kepada Masyarakat 2026
     */
    public function run(): void
    {
        $schemes = [
            // PENELITIAN DASAR
            [
                'name' => 'Penelitian Dosen Pemula (PDP)',
                'strata' => 'Dasar',
                'description' => 'Membina dan mengarahkan dosen pemula dalam penelitian',
            ],
            [
                'name' => 'Penelitian Pascasarjana - Tesis Magister (PTM)',
                'strata' => 'Dasar',
                'description' => 'Meningkatkan produktivitas mahasiswa S2 melalui penelitian tesis',
            ],
            [
                'name' => 'Penelitian Pascasarjana - Disertasi Doktor (PDD)',
                'strata' => 'Dasar',
                'description' => 'Meningkatkan produktivitas mahasiswa S3 melalui penelitian disertasi',
            ],
            [
                'name' => 'Penelitian Fundamental',
                'strata' => 'Dasar',
                'description' => 'Penelitian untuk pengembangan ilmu pengetahuan dan teknologi',
            ],
            [
                'name' => 'Penelitian Kerja Sama antar Perguruan Tinggi (PKPT)',
                'strata' => 'Dasar',
                'description' => 'Kerja sama penelitian antara perguruan tinggi pengirim dan mitra',
            ],

            // PENELITIAN TERAPAN
            [
                'name' => 'Penelitian Terapan - Luaran Prototipe',
                'strata' => 'Terapan',
                'description' => 'Penelitian yang menghasilkan prototipe teknologi atau produk',
            ],
            [
                'name' => 'Penelitian Terapan - Luaran Model',
                'strata' => 'Terapan',
                'description' => 'Penelitian yang menghasilkan model, kebijakan, atau karya seni',
            ],

            // PENGABDIAN KEPADA MASYARAKAT
            [
                'name' => 'Pemberdayaan Berbasis Masyarakat (PBM)',
                'strata' => 'PKM',
                'description' => 'Pemberdayaan kelompok masyarakat dalam pemecahan masalah',
            ],
            [
                'name' => 'Pemberdayaan Berbasis Kewirausahaan (PBK)',
                'strata' => 'PKM',
                'description' => 'Pemberdayaan kewirausahaan melalui UPUD atau kelompok usaha',
            ],
            [
                'name' => 'Pemberdayaan Berbasis Wilayah - Pemberdayaan Desa Binaan (PDB)',
                'strata' => 'PKM',
                'description' => 'Pemberdayaan desa binaan secara berkelanjutan',
            ],
            [
                'name' => 'Pemberdayaan Berbasis Wilayah - Pemberdayaan Wilayah (PW)',
                'strata' => 'PKM',
                'description' => 'Pemberdayaan wilayah melalui aplikasi ipteks',
            ],

            // INTERNAL SCHEMES
            [
                'name' => 'Penelitian Internal ITSNU',
                'strata' => 'Dasar',
                'description' => 'Penelitian yang didanai secara internal oleh ITSNU',
            ],
            [
                'name' => 'Pengabdian Internal ITSNU',
                'strata' => 'PKM',
                'description' => 'Pengabdian yang didanai secara internal oleh ITSNU',
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
