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
                'Jurnal Internasional Bereputasi',
                'Jurnal Internasional',
                'Jurnal Nasional Terakreditasi (Sinta 1-2)',
                'Jurnal Nasional Terakreditasi (Sinta 3-4)',
                'Jurnal Nasional Terakreditasi (Sinta 5-6)',
                'Prosiding Internasional Terindeks',
                'Prosiding Nasional',
                'HKI (Paten/Paten Sederhana)',
                'HKI (Hak Cipta/Merek/Desain Industri)',
                'Buku Ber-ISBN',
                'Purwarupa/Prototipe TRL 4-6',
                'Model/Purwarupa Sosial',
            ]),
            'target_status' => fake()->randomElement([
                'Accepted/Published',
                'Under Review',
                'Draft/Submitted',
                'Granted',
                'Registered',
            ]),
        ];
    }
}
