<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DailyNote>
 */
class DailyNoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'proposal_id' => \App\Models\Proposal::factory(),
            'activity_date' => fake()->dateTimeBetween('-6 months', 'now'),
            'activity_description' => fake()->paragraph(),
            'progress_percentage' => fake()->numberBetween(0, 100),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
