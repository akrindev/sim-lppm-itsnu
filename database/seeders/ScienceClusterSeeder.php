<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ScienceClusterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Level 1 - Main Clusters
        $teknik = \App\Models\ScienceCluster::create([
            'parent_id' => null,
            'level' => 1,
            'name' => 'Teknik',
        ]);

        $sains = \App\Models\ScienceCluster::create([
            'parent_id' => null,
            'level' => 1,
            'name' => 'Sains Alam',
        ]);

        $sosial = \App\Models\ScienceCluster::create([
            'parent_id' => null,
            'level' => 1,
            'name' => 'Sosial Humaniora',
        ]);

        // Level 2 - Sub Clusters (Teknik)
        $informatika = \App\Models\ScienceCluster::create([
            'parent_id' => $teknik->id,
            'level' => 2,
            'name' => 'Teknik Informatika',
        ]);

        $elektro = \App\Models\ScienceCluster::create([
            'parent_id' => $teknik->id,
            'level' => 2,
            'name' => 'Teknik Elektro',
        ]);

        // Level 2 - Sub Clusters (Sains)
        $fisika = \App\Models\ScienceCluster::create([
            'parent_id' => $sains->id,
            'level' => 2,
            'name' => 'Fisika',
        ]);

        $kimia = \App\Models\ScienceCluster::create([
            'parent_id' => $sains->id,
            'level' => 2,
            'name' => 'Kimia',
        ]);

        // Level 3 - Specific Areas
        \App\Models\ScienceCluster::create([
            'parent_id' => $informatika->id,
            'level' => 3,
            'name' => 'Kecerdasan Buatan',
        ]);

        \App\Models\ScienceCluster::create([
            'parent_id' => $informatika->id,
            'level' => 3,
            'name' => 'Jaringan Komputer',
        ]);

        \App\Models\ScienceCluster::create([
            'parent_id' => $elektro->id,
            'level' => 3,
            'name' => 'Sistem Tenaga',
        ]);

        \App\Models\ScienceCluster::create([
            'parent_id' => $fisika->id,
            'level' => 3,
            'name' => 'Fisika Material',
        ]);
    }
}
