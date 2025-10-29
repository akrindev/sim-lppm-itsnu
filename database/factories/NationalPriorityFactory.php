<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NationalPriority>
 */
class NationalPriorityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                'Pangan',
                'Energi',
                'Kesehatan',
                'Lingkungan',
                'Material Maju',
                'IT dan Komunikasi',
                'Pertahanan dan Keamanan',
                'Transportasi',
                'Kemaritiman',
            ]),
        ];
    }
}
