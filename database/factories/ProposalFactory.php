<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Proposal>
 */
class ProposalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(8),
            'submitter_id' => \App\Models\User::factory(),
            'detailable_type' => fake()->randomElement([
                \App\Models\Research::class,
                \App\Models\CommunityService::class,
            ]),
            'detailable_id' => null, // Will be set by morph relationship
            'research_scheme_id' => \App\Models\ResearchScheme::factory(),
            'focus_area_id' => \App\Models\FocusArea::factory(),
            'theme_id' => \App\Models\Theme::factory(),
            'topic_id' => \App\Models\Topic::factory(),
            'national_priority_id' => \App\Models\NationalPriority::factory(),
            'cluster_level1_id' => \App\Models\ScienceCluster::factory(),
            'cluster_level2_id' => \App\Models\ScienceCluster::factory()->level2(),
            'cluster_level3_id' => \App\Models\ScienceCluster::factory()->level3(),
            'sbk_value' => fake()->randomFloat(2, 5000000, 50000000),
            'duration_in_years' => fake()->numberBetween(1, 3),
            'summary' => fake()->paragraphs(3, true),
            'status' => fake()->randomElement(['draft', 'submitted', 'reviewed', 'approved', 'rejected', 'completed']),
        ];
    }
}
