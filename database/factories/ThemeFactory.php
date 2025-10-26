<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Theme>
 */
class ThemeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'focus_area_id' => \App\Models\FocusArea::factory(),
            'name' => fake()->randomElement([
                'Smart City',
                'E-Government',
                'Digital Economy',
                'Sustainable Agriculture',
                'Renewable Energy Systems',
                'Health Technology',
                'Educational Innovation',
            ]),
        ];
    }
}
