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
                'name' => 'Pangan',
                'description' => 'Riset ketahanan pangan dan pertanian: Padi, Jagung, Kedelai, Hortikultura, Peternakan, Pengolahan Pangan',
            ],
            [
                'name' => 'Energi',
                'description' => 'Riset energi baru dan terbarukan: Bahan Bakar Bersih, Teknologi Kelistrikan EBT, Konversi Energi, Biofuel',
            ],
            [
                'name' => 'Kesehatan',
                'description' => 'Riset kesehatan dan obat: Vaksin Nasional, OHT, Insulin, Paracetamol, Implan Tulang & Gigi, Alat Kesehatan, Bioteknologi Medis',
            ],
            [
                'name' => 'Transportasi',
                'description' => 'Riset sistem transportasi: Mobil Listrik Nasional, Pesawat N219 Amphibi, Kapal Nasional, Sistem Transportasi Modern',
            ],
            [
                'name' => 'Rekayasa Keteknikan',
                'description' => 'Riset engineering dan teknologi: Produk Manufaktur, Material Maju, Teknologi Tinggi, Industri Nasional',
            ],
            [
                'name' => 'Pertahanan dan Keamanan',
                'description' => 'Riset pertahanan dan keamanan: Sistem Pertahanan, Alutsista, Cybersecurity, Keamanan Strategis',
            ],
            [
                'name' => 'Kemaritiman',
                'description' => 'Riset kelautan dan perikanan: Sektor Kelautan, Perikanan, Monitoring Kelautan, Teknologi Pulau-Pulau Kecil',
            ],
            [
                'name' => 'Sosial-Humaniora-Pendidikan-Seni-Budaya',
                'description' => 'Riset sosial dan budaya: Riset Sosial, Hukum, Humaniora, Pendidikan SDM Unggul, Pelestarian Seni & Budaya',
            ],
            [
                'name' => 'Multidisiplin dan Lintas Sektoral',
                'description' => 'Riset multidisiplin: Perubahan Iklim, Mitigasi Bencana, Sumber Daya Air, Stunting, Isu Multidisiplin Lainnya',
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
