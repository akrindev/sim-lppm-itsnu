<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PartnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $partners = [
            ['name' => 'Dinas Kesehatan Kota Pekalongan', 'type' => 'Pemerintah', 'address' => 'Jl. Dr. Sutomo No. 22, Pekalongan'],
            ['name' => 'Dinas Pendidikan Provinsi Jawa Tengah', 'type' => 'Pemerintah', 'address' => 'Jl. Pemuda No. 134, Semarang'],
            ['name' => 'PT Telkom Indonesia', 'type' => 'Swasta', 'address' => 'Jl. Jenderal Gatot Subroto, Jakarta'],
            ['name' => 'CV Berkah Jaya', 'type' => 'Swasta', 'address' => 'Jl. Raya Pekalongan-Semarang KM 5'],
            ['name' => 'Koperasi Mitra Sejahtera', 'type' => 'Swasta', 'address' => 'Jl. Ahmad Yani No. 45, Pekalongan'],
            ['name' => 'Yayasan Pendidikan Indonesia', 'type' => 'Yayasan', 'address' => 'Jl. Sudirman No. 10, Jakarta'],
            ['name' => 'Kelompok Tani Maju Bersama', 'type' => 'Organisasi Masyarakat', 'address' => 'Desa Wonokerto, Pekalongan'],
            ['name' => 'Karang Taruna Desa Makmur', 'type' => 'Organisasi Masyarakat', 'address' => 'Desa Rowosari, Kendal'],
        ];

        foreach ($partners as $partner) {
            \App\Models\Partner::create($partner);
        }
    }
}
