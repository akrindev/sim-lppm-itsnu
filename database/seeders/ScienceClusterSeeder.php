<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ScienceClusterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Based on OECD Field of Science (FoS) Classification - 12 Rumpun Ilmu
     * Reference: Klasifikasi Rumpun Ilmu DIKTI/BAN-PT, aligned with OECD FoS
     * BIMA Kemdikbud alignment for national research standards
     *
     * Structure: 3 Levels
     * - Level 1: Rumpun Ilmu (Major Field) with codes 100-1200
     * - Level 2: Sub Rumpun (Subfield) with codes 110-1210
     * - Level 3: Bidang Ilmu (Detailed Field) with codes 111-1216
     */
    public function run(): void
    {
        // Clear existing data to ensure clean insert
        DB::table('science_clusters')->delete();

        // Complete 3-level structure for all 12 Rumpun Ilmu
        $clusters = [
            // ========== LEVEL 1: 12 RUMPUN ILMU ==========

            // 100 - Matematika dan Ilmu Pengetahuan Alam (MIPA)
            ['id' => 1, 'parent_id' => null, 'level' => 1, 'name' => '100 - Matematika dan Ilmu Pengetahuan Alam (MIPA)'],

            // 200 - Ilmu Tanaman
            ['id' => 2, 'parent_id' => null, 'level' => 1, 'name' => '200 - Ilmu Tanaman'],

            // 300 - Ilmu Hewani
            ['id' => 3, 'parent_id' => null, 'level' => 1, 'name' => '300 - Ilmu Hewani'],

            // 400 - Ilmu Kedokteran
            ['id' => 4, 'parent_id' => null, 'level' => 1, 'name' => '400 - Ilmu Kedokteran'],

            // 500 - Ilmu Kesehatan
            ['id' => 5, 'parent_id' => null, 'level' => 1, 'name' => '500 - Ilmu Kesehatan'],

            // 600 - Ilmu Teknik
            ['id' => 6, 'parent_id' => null, 'level' => 1, 'name' => '600 - Ilmu Teknik'],

            // 700 - Ilmu Bahasa
            ['id' => 7, 'parent_id' => null, 'level' => 1, 'name' => '700 - Ilmu Bahasa'],

            // 800 - Ilmu Ekonomi
            ['id' => 8, 'parent_id' => null, 'level' => 1, 'name' => '800 - Ilmu Ekonomi'],

            // 900 - Ilmu Sosial Humaniora
            ['id' => 9, 'parent_id' => null, 'level' => 1, 'name' => '900 - Ilmu Sosial Humaniora'],

            // 1000 - Agama dan Filsafat
            ['id' => 10, 'parent_id' => null, 'level' => 1, 'name' => '1000 - Agama dan Filsafat'],

            // 1100 - Seni, Desain, dan Media
            ['id' => 11, 'parent_id' => null, 'level' => 1, 'name' => '1100 - Seni, Desain, dan Media'],

            // 1200 - Ilmu Pendidikan
            ['id' => 12, 'parent_id' => null, 'level' => 1, 'name' => '1200 - Ilmu Pendidikan'],

            // ========== LEVEL 2: SUB RUMPUN ==========

            // MIPA (100) Sub-Clusters
            ['id' => 13, 'parent_id' => 1, 'level' => 2, 'name' => '110 - Ilmu IPA'],
            ['id' => 14, 'parent_id' => 1, 'level' => 2, 'name' => '120 - Matematika'],
            ['id' => 15, 'parent_id' => 1, 'level' => 2, 'name' => '130 - Kebumian dan Angkasa'],

            // Ilmu Tanaman (200) Sub-Clusters
            ['id' => 16, 'parent_id' => 2, 'level' => 2, 'name' => '210 - Agronomi dan Hortikultura'],
            ['id' => 17, 'parent_id' => 2, 'level' => 2, 'name' => '220 - Perlindungan Tanaman'],
            ['id' => 18, 'parent_id' => 2, 'level' => 2, 'name' => '230 - Ilmu Tanaman Industri'],

            // Ilmu Hewani (300) Sub-Clusters
            ['id' => 19, 'parent_id' => 3, 'level' => 2, 'name' => '310 - Peternakan'],
            ['id' => 20, 'parent_id' => 3, 'level' => 2, 'name' => '320 - Kedokteran Hewan'],
            ['id' => 21, 'parent_id' => 3, 'level' => 2, 'name' => '330 - Akuakultur'],

            // Ilmu Kedokteran (400) Sub-Clusters
            ['id' => 22, 'parent_id' => 4, 'level' => 2, 'name' => '410 - Kedokteran Dasar'],
            ['id' => 23, 'parent_id' => 4, 'level' => 2, 'name' => '420 - Kedokteran Klinik'],
            ['id' => 24, 'parent_id' => 4, 'level' => 2, 'name' => '430 - Kedokteran Spesialis'],

            // Ilmu Kesehatan (500) Sub-Clusters
            ['id' => 25, 'parent_id' => 5, 'level' => 2, 'name' => '510 - Keperawatan'],
            ['id' => 26, 'parent_id' => 5, 'level' => 2, 'name' => '520 - Gizi'],
            ['id' => 27, 'parent_id' => 5, 'level' => 2, 'name' => '530 - Farmasi'],
            ['id' => 28, 'parent_id' => 5, 'level' => 2, 'name' => '540 - Kesehatan Masyarakat'],

            // Ilmu Teknik (600) Sub-Clusters
            ['id' => 29, 'parent_id' => 6, 'level' => 2, 'name' => '610 - Teknik Sipil dan Perencanaan Tata Ruang'],
            ['id' => 30, 'parent_id' => 6, 'level' => 2, 'name' => '620 - Teknik Mesin dan Dirgantara'],
            ['id' => 31, 'parent_id' => 6, 'level' => 2, 'name' => '630 - Teknik Elektro dan Informatika'],
            ['id' => 32, 'parent_id' => 6, 'level' => 2, 'name' => '640 - Teknik Kimia dan Industri'],

            // Ilmu Bahasa (700) Sub-Clusters
            ['id' => 33, 'parent_id' => 7, 'level' => 2, 'name' => '710 - Sastra & Bahasa Indonesia/Daerah'],
            ['id' => 34, 'parent_id' => 7, 'level' => 2, 'name' => '720 - Ilmu Bahasa'],
            ['id' => 35, 'parent_id' => 7, 'level' => 2, 'name' => '730 - Bahasa Asing'],

            // Ilmu Ekonomi (800) Sub-Clusters
            ['id' => 36, 'parent_id' => 8, 'level' => 2, 'name' => '810 - Ekonomi'],
            ['id' => 37, 'parent_id' => 8, 'level' => 2, 'name' => '820 - Akuntansi'],
            ['id' => 38, 'parent_id' => 8, 'level' => 2, 'name' => '830 - Manajemen'],

            // Ilmu Sosial Humaniora (900) Sub-Clusters
            ['id' => 39, 'parent_id' => 9, 'level' => 2, 'name' => '910 - Ilmu Sosial'],
            ['id' => 40, 'parent_id' => 9, 'level' => 2, 'name' => '920 - Ilmu Hukum'],
            ['id' => 41, 'parent_id' => 9, 'level' => 2, 'name' => '930 - Ilmu Politik'],
            ['id' => 42, 'parent_id' => 9, 'level' => 2, 'name' => '940 - Sejarah dan Kependudukan'],

            // Agama dan Filsafat (1000) Sub-Clusters
            ['id' => 43, 'parent_id' => 10, 'level' => 2, 'name' => '1010 - Agama Islam'],
            ['id' => 44, 'parent_id' => 10, 'level' => 2, 'name' => '1020 - Agama Lain'],
            ['id' => 45, 'parent_id' => 10, 'level' => 2, 'name' => '1030 - Filsafat'],

            // Seni, Desain, dan Media (1100) Sub-Clusters
            ['id' => 46, 'parent_id' => 11, 'level' => 2, 'name' => '1110 - Seni Rupa dan Desain'],
            ['id' => 47, 'parent_id' => 11, 'level' => 2, 'name' => '1120 - Seni Pertunjukan'],
            ['id' => 48, 'parent_id' => 11, 'level' => 2, 'name' => '1130 - Media Komunikasi'],

            // Ilmu Pendidikan (1200) Sub-Clusters
            ['id' => 49, 'parent_id' => 12, 'level' => 2, 'name' => '1210 - Pendidikan Formal'],
            ['id' => 50, 'parent_id' => 12, 'level' => 2, 'name' => '1220 - Pendidikan Non Formal'],
            ['id' => 51, 'parent_id' => 12, 'level' => 2, 'name' => '1230 - Bimbingan dan Konseling'],

            // ========== LEVEL 3: BIDANG ILMU DETAIL ==========

            // Ilmu IPA (110) - Level 3
            ['id' => 52, 'parent_id' => 13, 'level' => 3, 'name' => '111 - Fisika'],
            ['id' => 53, 'parent_id' => 13, 'level' => 3, 'name' => '112 - Kimia'],
            ['id' => 54, 'parent_id' => 13, 'level' => 3, 'name' => '113 - Biologi (dan Bioteknologi Umum)'],
            ['id' => 55, 'parent_id' => 13, 'level' => 3, 'name' => '114 - IPA Lain'],

            // Matematika (120) - Level 3
            ['id' => 56, 'parent_id' => 14, 'level' => 3, 'name' => '121 - Matematika'],
            ['id' => 57, 'parent_id' => 14, 'level' => 3, 'name' => '122 - Statistik'],
            ['id' => 58, 'parent_id' => 14, 'level' => 3, 'name' => '123 - Ilmu Komputer'],
            ['id' => 59, 'parent_id' => 14, 'level' => 3, 'name' => '124 - Matematika Lain'],

            // Kebumian dan Angkasa (130) - Level 3
            ['id' => 60, 'parent_id' => 15, 'level' => 3, 'name' => '131 - Astronomi'],
            ['id' => 61, 'parent_id' => 15, 'level' => 3, 'name' => '132 - Geografi'],
            ['id' => 62, 'parent_id' => 15, 'level' => 3, 'name' => '133 - Geologi'],
            ['id' => 63, 'parent_id' => 15, 'level' => 3, 'name' => '134 - Geofisika'],
            ['id' => 64, 'parent_id' => 15, 'level' => 3, 'name' => '135 - Meteorologi'],
            ['id' => 65, 'parent_id' => 15, 'level' => 3, 'name' => '136 - Geofisika Lain'],

            // Agronomi dan Hortikultura (210) - Level 3
            ['id' => 66, 'parent_id' => 16, 'level' => 3, 'name' => '211 - Agronomi'],
            ['id' => 67, 'parent_id' => 16, 'level' => 3, 'name' => '212 - Hortikultura'],
            ['id' => 68, 'parent_id' => 16, 'level' => 3, 'name' => '213 - Pemuliaan Tanaman'],
            ['id' => 69, 'parent_id' => 16, 'level' => 3, 'name' => '214 - Ilmu Tanaman Pangan'],

            // Peternakan (310) - Level 3
            ['id' => 70, 'parent_id' => 19, 'level' => 3, 'name' => '311 - Produksi Ternak'],
            ['id' => 71, 'parent_id' => 19, 'level' => 3, 'name' => '312 - Nutrisi Ternak'],
            ['id' => 72, 'parent_id' => 19, 'level' => 3, 'name' => '313 - Teknologi Hasil Ternak'],
            ['id' => 73, 'parent_id' => 19, 'level' => 3, 'name' => '314 - Sosial Ekonomi Peternakan'],

            // Kedokteran Klinik (420) - Level 3
            ['id' => 74, 'parent_id' => 23, 'level' => 3, 'name' => '421 - Kedokteran Umum'],
            ['id' => 75, 'parent_id' => 23, 'level' => 3, 'name' => '422 - Kedokteran Anak'],
            ['id' => 76, 'parent_id' => 23, 'level' => 3, 'name' => '423 - Kedokteran Bedah'],
            ['id' => 77, 'parent_id' => 23, 'level' => 3, 'name' => '424 - Kedokteran Gigi'],

            // Teknik Sipil (610) - Level 3
            ['id' => 78, 'parent_id' => 29, 'level' => 3, 'name' => '611 - Teknik Sipil'],
            ['id' => 79, 'parent_id' => 29, 'level' => 3, 'name' => '612 - Teknik Lingkungan'],
            ['id' => 80, 'parent_id' => 29, 'level' => 3, 'name' => '613 - Arsitektur'],
            ['id' => 81, 'parent_id' => 29, 'level' => 3, 'name' => '614 - Perencanaan Wilayah dan Kota'],

            // Teknik Elektro dan Informatika (630) - Level 3
            ['id' => 82, 'parent_id' => 31, 'level' => 3, 'name' => '631 - Teknik Elektro'],
            ['id' => 83, 'parent_id' => 31, 'level' => 3, 'name' => '632 - Teknik Informatika'],
            ['id' => 84, 'parent_id' => 31, 'level' => 3, 'name' => '633 - Teknik Komputer'],
            ['id' => 85, 'parent_id' => 31, 'level' => 3, 'name' => '634 - Sistem Informasi'],

            // Bahasa Asing (730) - Level 3
            ['id' => 86, 'parent_id' => 35, 'level' => 3, 'name' => '731 - Sastra/Bahasa Inggris'],
            ['id' => 87, 'parent_id' => 35, 'level' => 3, 'name' => '732 - Sastra/Bahasa Jepang'],
            ['id' => 88, 'parent_id' => 35, 'level' => 3, 'name' => '733 - Sastra/Bahasa China (Mandarin)'],
            ['id' => 89, 'parent_id' => 35, 'level' => 3, 'name' => '734 - Sastra/Bahasa Arab'],
            ['id' => 90, 'parent_id' => 35, 'level' => 3, 'name' => '735 - Sastra/Bahasa Korea'],
            ['id' => 91, 'parent_id' => 35, 'level' => 3, 'name' => '736 - Sastra/Bahasa Jerman'],
            ['id' => 92, 'parent_id' => 35, 'level' => 3, 'name' => '737 - Sastra/Bahasa Melayu'],
            ['id' => 93, 'parent_id' => 35, 'level' => 3, 'name' => '738 - Sastra/Bahasa Belanda'],
            ['id' => 94, 'parent_id' => 35, 'level' => 3, 'name' => '739 - Sastra/Bahasa Perancis'],

            // Ekonomi (810) - Level 3
            ['id' => 95, 'parent_id' => 36, 'level' => 3, 'name' => '811 - Ekonomi Pembangunan'],
            ['id' => 96, 'parent_id' => 36, 'level' => 3, 'name' => '812 - Ekonomi Moneter'],
            ['id' => 97, 'parent_id' => 36, 'level' => 3, 'name' => '813 - Ekonomi Internasional'],
            ['id' => 98, 'parent_id' => 36, 'level' => 3, 'name' => '814 - Ekonomi Syariah'],

            // Manajemen (830) - Level 3
            ['id' => 99, 'parent_id' => 38, 'level' => 3, 'name' => '831 - Manajemen SDM'],
            ['id' => 100, 'parent_id' => 38, 'level' => 3, 'name' => '832 - Manajemen Pemasaran'],
            ['id' => 101, 'parent_id' => 38, 'level' => 3, 'name' => '833 - Manajemen Keuangan'],
            ['id' => 102, 'parent_id' => 38, 'level' => 3, 'name' => '834 - Manajemen Operasional'],

            // Ilmu Sosial (910) - Level 3
            ['id' => 103, 'parent_id' => 39, 'level' => 3, 'name' => '911 - Sosiologi'],
            ['id' => 104, 'parent_id' => 39, 'level' => 3, 'name' => '912 - Antropologi'],
            ['id' => 105, 'parent_id' => 39, 'level' => 3, 'name' => '913 - Kriminologi'],
            ['id' => 106, 'parent_id' => 39, 'level' => 3, 'name' => '914 - komunikasi'],

            // Pendidikan Formal (1210) - Level 3
            ['id' => 107, 'parent_id' => 49, 'level' => 3, 'name' => '1211 - Pendidikan Anak Usia Dini'],
            ['id' => 108, 'parent_id' => 49, 'level' => 3, 'name' => '1212 - Pendidikan Dasar'],
            ['id' => 109, 'parent_id' => 49, 'level' => 3, 'name' => '1213 - Pendidikan Menengah'],
            ['id' => 110, 'parent_id' => 49, 'level' => 3, 'name' => '1214 - Pendidikan Tinggi'],

            // Seni Rupa dan Desain (1110) - Level 3
            ['id' => 111, 'parent_id' => 46, 'level' => 3, 'name' => '1111 - Seni Lukis'],
            ['id' => 112, 'parent_id' => 46, 'level' => 3, 'name' => '1112 - Seni Patung'],
            ['id' => 113, 'parent_id' => 46, 'level' => 3, 'name' => '1113 - Desain Produk'],
            ['id' => 114, 'parent_id' => 46, 'level' => 3, 'name' => '1114 - Desain Komunikasi Visual'],
        ];

        // Prepare data for batch insert without the explicit IDs
        $insertData = array_map(function ($cluster) {
            return [
                'name' => $cluster['name'],
                'level' => $cluster['level'],
                'parent_id' => $cluster['parent_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $clusters);

        // Batch insert all clusters
        \App\Models\ScienceCluster::insert($insertData);

        $this->command->info('Science clusters seeded successfully!');
        $this->command->info('Level 1: 12 Rumpun Ilmu');
        $this->command->info('Level 2: '.(count(array_filter($clusters, fn ($c) => $c['level'] == 2))).' Sub Rumpun');
        $this->command->info('Level 3: '.(count(array_filter($clusters, fn ($c) => $c['level'] == 3))).' Bidang Ilmu Detail');
        $this->command->info('Total: '.count($clusters).' clusters seeded');
    }
}
