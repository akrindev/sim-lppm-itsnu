<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Research>
 */
class ResearchFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'macro_research_group_id' => \App\Models\MacroResearchGroup::inRandomOrder()->first()?->id,
            'tkt_type' => fake()->randomElement([
                'Farmasi/Obat',
                'Kesehatan - Alat Kesehatan',
                'Kesehatan - Produk Vaksin/Hayati',
                'Pertanian/Perikanan/Peternakan',
                'Seni',
                'Software',
                'Sosial Humaniora dan Pendidikan',
                'Umum',
            ]),
            'background' => fake()->paragraphs(3, true),
            'state_of_the_art' => fake()->paragraphs(2, true),
            'methodology' => fake()->paragraphs(2, true),
            'roadmap_data' => [
                'year_1' => fake()->sentence(10),
                'year_2' => fake()->sentence(10),
                'year_3' => fake()->sentence(10),
            ],
        ];
    }
}
