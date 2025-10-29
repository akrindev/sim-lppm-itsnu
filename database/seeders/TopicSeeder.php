<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TopicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $themes = \App\Models\Theme::all();

        if ($themes->isEmpty()) {
            return;
        }

        $topics = [
            'IoT untuk Smart City',
            'Blockchain untuk E-Government',
            'AI dalam Pendidikan',
            'Machine Learning untuk Prediksi',
            'Solar Panel Efficiency',
            'Wind Turbine Optimization',
            'Hydroponic Systems',
            'Vertical Farming',
            'Telemedicine Platform',
            'Digital Health Records',
            'E-Learning Systems',
            'Mobile Learning Apps',
        ];

        foreach ($themes as $theme) {
            // Create 2-3 topics per theme
            $topicCount = rand(2, 3);
            for ($i = 0; $i < $topicCount; $i++) {
                \App\Models\Topic::create([
                    'theme_id' => $theme->id,
                    'name' => fake()->randomElement($topics),
                ]);
            }
        }
    }
}
