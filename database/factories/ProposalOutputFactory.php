<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProposalOutput>
 */
class ProposalOutputFactory extends Factory
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
            'output_year' => fake()->numberBetween(1, 3),
            'category' => fake()->randomElement(['Wajib', 'Tambahan']),
            'type' => fake()->randomElement([
                'Jurnal Internasional',
                'Jurnal Nasional',
                'Prosiding',
                'HKI',
                'Paten',
                'Buku',
                'Produk',
            ]),
            'target_status' => fake()->randomElement([
                'Q1',
                'Q2',
                'Q3',
                'Q4',
                'Sinta 1',
                'Sinta 2',
                'Granted',
                'Registered',
                'Published',
            ]),
        ];
    }
}
