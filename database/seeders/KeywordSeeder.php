<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class KeywordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $keywords = [
            'Machine Learning',
            'Internet of Things',
            'Blockchain',
            'Artificial Intelligence',
            'Big Data',
            'Cloud Computing',
            'Cybersecurity',
            'Data Mining',
            'Energi Terbarukan',
            'Energi Surya',
            'Biomassa',
            'Panel Surya',
            'Pertanian Organik',
            'Ketahanan Pangan',
            'Hidroponik',
            'Aquaponik',
            'Ekonomi Kreatif',
            'UMKM',
            'Pemberdayaan Masyarakat',
            'Kewirausahaan',
            'Kesehatan Masyarakat',
            'Gizi',
            'Sanitasi',
            'Telemedicine',
            'Pendidikan Karakter',
            'Literasi Digital',
            'Pembelajaran Daring',
            'E-Learning',
            'Smart City',
            'E-Government',
            'Digital Economy',
            'Fintech',
        ];

        foreach ($keywords as $keyword) {
            \App\Models\Keyword::firstOrCreate(['name' => $keyword]);
        }
    }
}
