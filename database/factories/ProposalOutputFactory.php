<?php

namespace Database\Factories;

use App\Constants\ProposalConstants;
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
        $group = fake()->randomElement(ProposalConstants::RESEARCH_OUTPUT_GROUPS);
        $types = ProposalConstants::RESEARCH_OUTPUT_TYPES[$group] ?? ['Lainnya'];

        return [
            'proposal_id' => \App\Models\Proposal::factory(),
            'output_year' => fake()->numberBetween(1, 3),
            'category' => fake()->randomElement(ProposalConstants::OUTPUT_CATEGORIES),
            'group' => $group,
            'type' => fake()->randomElement($types),
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
