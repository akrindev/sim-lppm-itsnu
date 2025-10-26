<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FocusArea>
 */
class FocusAreaFactory extends Factory
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
                'Teknologi Informasi dan Komunikasi',
                'Energi Terbarukan',
                'Ketahanan Pangan',
                'Kesehatan dan Obat',
                'Transportasi',
                'Pertahanan dan Keamanan',
                'Material Maju',
                'Kemaritiman',
            ]),
        ];
    }
}
