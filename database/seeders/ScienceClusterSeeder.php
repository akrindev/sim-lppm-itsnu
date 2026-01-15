<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ScienceClusterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Based on OECD Field of Science (FoS) Classification - 12 Rumpun Ilmu
     * Reference: Klasifikasi Rumpun Ilmu DIKTI/BAN-PT, aligned with OECD FoS
     * BIMA Kemdiktisaintek alignment for national research standards 2026
     *
     * Structure: 3 Levels
     * - Level 1: Rumpun Ilmu (Major Field) with codes 100-1200
     * - Level 2: Sub Rumpun (Subfield) with codes 110-1210
     * - Level 3: Bidang Ilmu (Detailed Field) with codes 111-1216
     */
    public function run(): void
    {
        // Complete 3-level structure for all 12 Rumpun Ilmu
        $clusters = [
            // ========== LEVEL 1: 12 RUMPUN ILMU ==========

            // Matematika dan Ilmu Pengetahuan Alam (MIPA)
            ['id' => 1, 'parent_id' => null, 'level' => 1, 'name' => 'Matematika dan Ilmu Pengetahuan Alam (MIPA)'],

            // Ilmu Tanaman
            ['id' => 2, 'parent_id' => null, 'level' => 1, 'name' => 'Ilmu Tanaman'],

            // Ilmu Hewani
            ['id' => 3, 'parent_id' => null, 'level' => 1, 'name' => 'Ilmu Hewani'],

            // Ilmu Kedokteran
            ['id' => 4, 'parent_id' => null, 'level' => 1, 'name' => 'Ilmu Kedokteran'],

            // Ilmu Kesehatan
            ['id' => 5, 'parent_id' => null, 'level' => 1, 'name' => 'Ilmu Kesehatan'],

            // Ilmu Teknik
            ['id' => 6, 'parent_id' => null, 'level' => 1, 'name' => 'Ilmu Teknik'],

            // Ilmu Bahasa
            ['id' => 7, 'parent_id' => null, 'level' => 1, 'name' => 'Ilmu Bahasa'],

            // Ilmu Ekonomi
            ['id' => 8, 'parent_id' => null, 'level' => 1, 'name' => 'Ilmu Ekonomi'],

            // Ilmu Sosial Humaniora
            ['id' => 9, 'parent_id' => null, 'level' => 1, 'name' => 'Ilmu Sosial Humaniora'],

            // Agama dan Filsafat
            ['id' => 10, 'parent_id' => null, 'level' => 1, 'name' => 'Agama dan Filsafat'],

            // Seni, Desain, dan Media
            ['id' => 11, 'parent_id' => null, 'level' => 1, 'name' => 'Seni, Desain, dan Media'],

            // Ilmu Pendidikan
            ['id' => 12, 'parent_id' => null, 'level' => 1, 'name' => 'Ilmu Pendidikan'],

            // ========== LEVEL 2: SUB RUMPUN ==========

            // MIPA Sub-Clusters
            ['id' => 13, 'parent_id' => 1, 'level' => 2, 'name' => 'Ilmu IPA'],
            ['id' => 14, 'parent_id' => 1, 'level' => 2, 'name' => 'Matematika'],
            ['id' => 15, 'parent_id' => 1, 'level' => 2, 'name' => 'Kebumian dan Angkasa'],

            // Ilmu Tanaman Sub-Clusters
            ['id' => 16, 'parent_id' => 2, 'level' => 2, 'name' => 'Agronomi dan Hortikultura'],
            ['id' => 17, 'parent_id' => 2, 'level' => 2, 'name' => 'Perlindungan Tanaman'],
            ['id' => 18, 'parent_id' => 2, 'level' => 2, 'name' => 'Ilmu Tanaman Industri'],

            // Ilmu Hewani Sub-Clusters
            ['id' => 19, 'parent_id' => 3, 'level' => 2, 'name' => 'Peternakan'],
            ['id' => 20, 'parent_id' => 3, 'level' => 2, 'name' => 'Kedokteran Hewan'],
            ['id' => 21, 'parent_id' => 3, 'level' => 2, 'name' => 'Akuakultur'],

            // Ilmu Kedokteran Sub-Clusters
            ['id' => 22, 'parent_id' => 4, 'level' => 2, 'name' => 'Kedokteran Dasar'],
            ['id' => 23, 'parent_id' => 4, 'level' => 2, 'name' => 'Kedokteran Klinik'],
            ['id' => 24, 'parent_id' => 4, 'level' => 2, 'name' => 'Kedokteran Spesialis'],

            // Ilmu Kesehatan Sub-Clusters
            ['id' => 25, 'parent_id' => 5, 'level' => 2, 'name' => 'Keperawatan'],
            ['id' => 26, 'parent_id' => 5, 'level' => 2, 'name' => 'Gizi'],
            ['id' => 27, 'parent_id' => 5, 'level' => 2, 'name' => 'Farmasi'],
            ['id' => 28, 'parent_id' => 5, 'level' => 2, 'name' => 'Kesehatan Masyarakat'],

            // Ilmu Teknik Sub-Clusters
            ['id' => 29, 'parent_id' => 6, 'level' => 2, 'name' => 'Teknik Sipil dan Perencanaan Tata Ruang'],
            ['id' => 30, 'parent_id' => 6, 'level' => 2, 'name' => 'Teknik Mesin dan Dirgantara'],
            ['id' => 31, 'parent_id' => 6, 'level' => 2, 'name' => 'Teknik Elektro dan Informatika'],
            ['id' => 32, 'parent_id' => 6, 'level' => 2, 'name' => 'Teknik Kimia dan Industri'],

            // Ilmu Bahasa Sub-Clusters
            ['id' => 33, 'parent_id' => 7, 'level' => 2, 'name' => 'Sastra & Bahasa Indonesia/Daerah'],
            ['id' => 34, 'parent_id' => 7, 'level' => 2, 'name' => 'Ilmu Bahasa'],
            ['id' => 35, 'parent_id' => 7, 'level' => 2, 'name' => 'Bahasa Asing'],

            // Ilmu Ekonomi Sub-Clusters
            ['id' => 36, 'parent_id' => 8, 'level' => 2, 'name' => 'Ekonomi'],
            ['id' => 37, 'parent_id' => 8, 'level' => 2, 'name' => 'Akuntansi'],
            ['id' => 38, 'parent_id' => 8, 'level' => 2, 'name' => 'Manajemen'],

            // Ilmu Sosial Humaniora Sub-Clusters
            ['id' => 39, 'parent_id' => 9, 'level' => 2, 'name' => 'Ilmu Sosial'],
            ['id' => 40, 'parent_id' => 9, 'level' => 2, 'name' => 'Ilmu Hukum'],
            ['id' => 41, 'parent_id' => 9, 'level' => 2, 'name' => 'Ilmu Politik'],
            ['id' => 42, 'parent_id' => 9, 'level' => 2, 'name' => 'Sejarah dan Kependudukan'],

            // Agama dan Filsafat Sub-Clusters
            ['id' => 43, 'parent_id' => 10, 'level' => 2, 'name' => 'Agama Islam'],
            ['id' => 44, 'parent_id' => 10, 'level' => 2, 'name' => 'Agama Lain'],
            ['id' => 45, 'parent_id' => 10, 'level' => 2, 'name' => 'Filsafat'],

            // Seni, Desain, dan Media Sub-Clusters
            ['id' => 46, 'parent_id' => 11, 'level' => 2, 'name' => 'Seni Rupa dan Desain'],
            ['id' => 47, 'parent_id' => 11, 'level' => 2, 'name' => 'Seni Pertunjukan'],
            ['id' => 48, 'parent_id' => 11, 'level' => 2, 'name' => 'Media Komunikasi'],

            // Ilmu Pendidikan Sub-Clusters
            ['id' => 49, 'parent_id' => 12, 'level' => 2, 'name' => 'Pendidikan Formal'],
            ['id' => 50, 'parent_id' => 12, 'level' => 2, 'name' => 'Pendidikan Non Formal'],
            ['id' => 51, 'parent_id' => 12, 'level' => 2, 'name' => 'Bimbingan dan Konseling'],

            // ========== LEVEL 3: BIDANG ILMU DETAIL ==========

            // Ilmu IPA - Level 3
            ['id' => 52, 'parent_id' => 13, 'level' => 3, 'name' => 'Fisika'],
            ['id' => 53, 'parent_id' => 13, 'level' => 3, 'name' => 'Kimia'],
            ['id' => 54, 'parent_id' => 13, 'level' => 3, 'name' => 'Biologi (dan Bioteknologi Umum)'],
            ['id' => 55, 'parent_id' => 13, 'level' => 3, 'name' => 'IPA Lain'],

            // Matematika - Level 3
            ['id' => 56, 'parent_id' => 14, 'level' => 3, 'name' => 'Matematika'],
            ['id' => 57, 'parent_id' => 14, 'level' => 3, 'name' => 'Statistik'],
            ['id' => 58, 'parent_id' => 14, 'level' => 3, 'name' => 'Ilmu Komputer'],
            ['id' => 59, 'parent_id' => 14, 'level' => 3, 'name' => 'Matematika Lain'],

            // Kebumian dan Angkasa - Level 3
            ['id' => 60, 'parent_id' => 15, 'level' => 3, 'name' => 'Astronomi'],
            ['id' => 61, 'parent_id' => 15, 'level' => 3, 'name' => 'Geografi'],
            ['id' => 62, 'parent_id' => 15, 'level' => 3, 'name' => 'Geologi'],
            ['id' => 63, 'parent_id' => 15, 'level' => 3, 'name' => 'Geofisika'],
            ['id' => 64, 'parent_id' => 15, 'level' => 3, 'name' => 'Meteorologi'],
            ['id' => 65, 'parent_id' => 15, 'level' => 3, 'name' => 'Geofisika Lain'],

            // Agronomi dan Hortikultura - Level 3
            ['id' => 66, 'parent_id' => 16, 'level' => 3, 'name' => 'Agronomi'],
            ['id' => 67, 'parent_id' => 16, 'level' => 3, 'name' => 'Hortikultura'],
            ['id' => 68, 'parent_id' => 16, 'level' => 3, 'name' => 'Pemuliaan Tanaman'],
            ['id' => 69, 'parent_id' => 16, 'level' => 3, 'name' => 'Ilmu Tanaman Pangan'],

            // Peternakan - Level 3
            ['id' => 70, 'parent_id' => 19, 'level' => 3, 'name' => 'Produksi Ternak'],
            ['id' => 71, 'parent_id' => 19, 'level' => 3, 'name' => 'Nutrisi Ternak'],
            ['id' => 72, 'parent_id' => 19, 'level' => 3, 'name' => 'Teknologi Hasil Ternak'],
            ['id' => 73, 'parent_id' => 19, 'level' => 3, 'name' => 'Sosial Ekonomi Peternakan'],

            // Kedokteran Klinik - Level 3
            ['id' => 74, 'parent_id' => 23, 'level' => 3, 'name' => 'Kedokteran Umum'],
            ['id' => 75, 'parent_id' => 23, 'level' => 3, 'name' => 'Kedokteran Anak'],
            ['id' => 76, 'parent_id' => 23, 'level' => 3, 'name' => 'Kedokteran Bedah'],
            ['id' => 77, 'parent_id' => 23, 'level' => 3, 'name' => 'Kedokteran Gigi'],

            // Teknik Sipil - Level 3
            ['id' => 78, 'parent_id' => 29, 'level' => 3, 'name' => 'Teknik Sipil'],
            ['id' => 79, 'parent_id' => 29, 'level' => 3, 'name' => 'Teknik Lingkungan'],
            ['id' => 80, 'parent_id' => 29, 'level' => 3, 'name' => 'Arsitektur'],
            ['id' => 81, 'parent_id' => 29, 'level' => 3, 'name' => 'Perencanaan Wilayah dan Kota'],

            // Teknik Elektro dan Informatika - Level 3
            ['id' => 82, 'parent_id' => 31, 'level' => 3, 'name' => 'Teknik Elektro'],
            ['id' => 83, 'parent_id' => 31, 'level' => 3, 'name' => 'Teknik Informatika'],
            ['id' => 84, 'parent_id' => 31, 'level' => 3, 'name' => 'Teknik Komputer'],
            ['id' => 85, 'parent_id' => 31, 'level' => 3, 'name' => 'Sistem Informasi'],

            // Bahasa Asing - Level 3
            ['id' => 86, 'parent_id' => 35, 'level' => 3, 'name' => 'Sastra/Bahasa Inggris'],
            ['id' => 87, 'parent_id' => 35, 'level' => 3, 'name' => 'Sastra/Bahasa Jepang'],
            ['id' => 88, 'parent_id' => 35, 'level' => 3, 'name' => 'Sastra/Bahasa China (Mandarin)'],
            ['id' => 89, 'parent_id' => 35, 'level' => 3, 'name' => 'Sastra/Bahasa Arab'],
            ['id' => 90, 'parent_id' => 35, 'level' => 3, 'name' => 'Sastra/Bahasa Korea'],
            ['id' => 91, 'parent_id' => 35, 'level' => 3, 'name' => 'Sastra/Bahasa Jerman'],
            ['id' => 92, 'parent_id' => 35, 'level' => 3, 'name' => 'Sastra/Bahasa Melayu'],
            ['id' => 93, 'parent_id' => 35, 'level' => 3, 'name' => 'Sastra/Bahasa Belanda'],
            ['id' => 94, 'parent_id' => 35, 'level' => 3, 'name' => 'Sastra/Bahasa Perancis'],

            // Ekonomi - Level 3
            ['id' => 95, 'parent_id' => 36, 'level' => 3, 'name' => 'Ekonomi Pembangunan'],
            ['id' => 96, 'parent_id' => 36, 'level' => 3, 'name' => 'Ekonomi Moneter'],
            ['id' => 97, 'parent_id' => 36, 'level' => 3, 'name' => 'Ekonomi Internasional'],
            ['id' => 98, 'parent_id' => 36, 'level' => 3, 'name' => 'Ekonomi Syariah'],

            // Manajemen - Level 3
            ['id' => 99, 'parent_id' => 38, 'level' => 3, 'name' => 'Manajemen SDM'],
            ['id' => 100, 'parent_id' => 38, 'level' => 3, 'name' => 'Manajemen Pemasaran'],
            ['id' => 101, 'parent_id' => 38, 'level' => 3, 'name' => 'Manajemen Keuangan'],
            ['id' => 102, 'parent_id' => 38, 'level' => 3, 'name' => 'Manajemen Operasional'],

            // Ilmu Sosial - Level 3
            ['id' => 103, 'parent_id' => 39, 'level' => 3, 'name' => 'Sosiologi'],
            ['id' => 104, 'parent_id' => 39, 'level' => 3, 'name' => 'Antropologi'],
            ['id' => 105, 'parent_id' => 39, 'level' => 3, 'name' => 'Kriminologi'],
            ['id' => 106, 'parent_id' => 39, 'level' => 3, 'name' => 'komunikasi'],

            // Pendidikan Formal - Level 3
            ['id' => 107, 'parent_id' => 49, 'level' => 3, 'name' => 'Pendidikan Anak Usia Dini'],
            ['id' => 108, 'parent_id' => 49, 'level' => 3, 'name' => 'Pendidikan Dasar'],
            ['id' => 109, 'parent_id' => 49, 'level' => 3, 'name' => 'Pendidikan Menengah'],
            ['id' => 110, 'parent_id' => 49, 'level' => 3, 'name' => 'Pendidikan Tinggi'],

            // Seni Rupa dan Desain - Level 3
            ['id' => 111, 'parent_id' => 46, 'level' => 3, 'name' => 'Seni Lukis'],
            ['id' => 112, 'parent_id' => 46, 'level' => 3, 'name' => 'Seni Patung'],
            ['id' => 113, 'parent_id' => 46, 'level' => 3, 'name' => 'Desain Produk'],
            ['id' => 114, 'parent_id' => 46, 'level' => 3, 'name' => 'Desain Komunikasi Visual'],
        ];

        // Prepare data for upsert - include all fields needed
        $upsertData = array_map(function ($cluster) {
            return [
                'id' => $cluster['id'],
                'name' => $cluster['name'],
                'level' => $cluster['level'],
                'parent_id' => $cluster['parent_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $clusters);

        // Upsert all clusters - will update if exists (by id), insert if not
        \App\Models\ScienceCluster::upsert($upsertData, ['id'], ['name', 'level', 'parent_id', 'updated_at']);

        $this->command->info('Science clusters seeded successfully!');
        $this->command->info('Level 1: 12 Rumpun Ilmu');
        $this->command->info('Level 2: '.(count(array_filter($clusters, fn ($c) => $c['level'] == 2))).' Sub Rumpun');
        $this->command->info('Level 3: '.(count(array_filter($clusters, fn ($c) => $c['level'] == 3))).' Bidang Ilmu Detail');
        $this->command->info('Total: '.count($clusters).' clusters seeded');
    }
}
