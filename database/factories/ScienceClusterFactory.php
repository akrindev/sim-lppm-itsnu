<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ScienceCluster>
 */
class ScienceClusterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'parent_id' => null,
            'level' => 1,
            'name' => fake()->randomElement([
                'Teknik',
                'Sains Alam',
                'Sosial Humaniora',
                'Kedokteran dan Kesehatan',
                'Pertanian',
                'Ekonomi dan Bisnis',
            ]),
        ];
    }

    public function level2(): static
    {
        return $this->state(fn (array $attributes) => [
            'level' => 2,
            'parent_id' => \App\Models\ScienceCluster::factory(),
        ]);
    }

    public function level3(): static
    {
        return $this->state(fn (array $attributes) => [
            'level' => 3,
            'parent_id' => \App\Models\ScienceCluster::factory()->level2(),
        ]);
    }
}
