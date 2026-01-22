<?php

namespace Database\Seeders;

use App\Models\ReviewCriteria;
use Illuminate\Database\Seeder;

class ReviewCriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $researchCriteria = [
            [
                'criteria' => 'Ringkasan',
                'description' => 'Judul, Tujuan, Metode Penelitian, dan Target Luaran. Kata kunci : 5 kata',
                'weight' => 10,
                'order' => 1,
            ],
            [
                'criteria' => 'Latar Belakang',
                'description' => 'Permasalahan; Keaslian Penelitian; Manfaat yang Diharapkan; Tujuan Penelitian, Roadmap penelitian dan kontribusi terhadap ilmu pengetahuan',
                'weight' => 30,
                'order' => 2,
            ],
            [
                'criteria' => 'Tinjauan Pustaka',
                'description' => 'Tinjauan Pustaka yang mutakhir (Jurnal 5-10 tahun terakhir)',
                'weight' => 25,
                'order' => 3,
            ],
            [
                'criteria' => 'Metode Penelitian',
                'description' => 'Metode Pengumpulan, Analisis Data penelitian, lokasi penelitian, peubah yang diamati/diukur, dan model, rancangan penelitian',
                'weight' => 25,
                'order' => 4,
            ],
            [
                'criteria' => 'Daftar Pustaka Dan Lampiran',
                'description' => '(Jurnal 5-10 tahun terakhir), Jadwal Penelitian, Perkiraan Biaya dan biodata',
                'weight' => 10,
                'order' => 5,
            ],
        ];

        foreach ($researchCriteria as $criteria) {
            ReviewCriteria::create(array_merge($criteria, ['type' => 'research']));
        }

        $pkmCriteria = [
            [
                'criteria' => 'Ringkasan',
                'description' => 'Judul, Tujuan, Metode, dan Target Luaran. Kata kunci : 5 kata',
                'weight' => 15,
                'order' => 1,
            ],
            [
                'criteria' => 'Perumusan Masalah',
                'description' => 'Ketajaman Perumusan Masalah dan tujuan PkM',
                'weight' => 15,
                'order' => 2,
            ],
            [
                'criteria' => 'Manfaat hasil PkM',
                'description' => 'Pengembangan IPTEKS, pembangunan, dan/atau pengembangan kelembagaan',
                'weight' => 20,
                'order' => 3,
            ],
            [
                'criteria' => 'Metode Pelaksanaan',
                'description' => 'Metode Pengabdian Masyarakat',
                'weight' => 20,
                'order' => 4,
            ],
            [
                'criteria' => 'Output dan Outcome PkM',
                'description' => 'Rumusan Output pengabdian dan outcome pengabdian masyarakat',
                'weight' => 30,
                'order' => 5,
            ],
        ];

        foreach ($pkmCriteria as $criteria) {
            ReviewCriteria::create(array_merge($criteria, ['type' => 'community_service']));
        }
    }
}
