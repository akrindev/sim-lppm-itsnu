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
            'final_tkt_target' => fake()->numberBetween(1, 9),
            'background' => fake()->paragraphs(3, true),
            'state_of_the_art' => fake()->paragraphs(2, true),
            'methodology' => fake()->randomElement([
                'Kuantitatif',
                'Kualitatif',
                'Mixed Method',
                'Eksperimen',
                'Studi Kasus',
                'Research and Development',
            ]),
            'roadmap_data' => [
                'year_1' => fake()->sentence(10),
                'year_2' => fake()->sentence(10),
                'year_3' => fake()->sentence(10),
            ],
        ];
    }
}
