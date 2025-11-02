<?php

namespace Database\Seeders;

use App\Models\MacroResearchGroup;
use Illuminate\Database\Seeder;

class MacroResearchGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groups = [
            [
                'name' => 'Kesehatan dan Kedokteran',
                'description' => 'Penelitian terkait kesehatan, kedokteran, farmasi, dan kesehatan masyarakat',
            ],
            [
                'name' => 'Sains dan Teknologi',
                'description' => 'Penelitian di bidang sains murni, teknologi informasi, dan rekayasa',
            ],
            [
                'name' => 'Sosial dan Humaniora',
                'description' => 'Penelitian sosial, budaya, pendidikan, hukum, dan humaniora',
            ],
            [
                'name' => 'Ekonomi dan Bisnis',
                'description' => 'Penelitian ekonomi, manajemen, akuntansi, dan kewirausahaan',
            ],
            [
                'name' => 'Pertanian dan Lingkungan',
                'description' => 'Penelitian pertanian, perikanan, kehutanan, dan lingkungan hidup',
            ],
            [
                'name' => 'Keislaman',
                'description' => 'Penelitian studi Islam, pendidikan Islam, ekonomi syariah, dan dakwah',
            ],
        ];

        foreach ($groups as $group) {
            MacroResearchGroup::create($group);
        }
    }
}
