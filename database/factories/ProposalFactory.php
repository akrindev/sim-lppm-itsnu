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
            'research_scheme_id' => \App\Models\ResearchScheme::inRandomOrder()->first()?->id ?? \App\Models\ResearchScheme::factory(),
            'focus_area_id' => \App\Models\FocusArea::inRandomOrder()->first()?->id ?? \App\Models\FocusArea::factory(),
            'theme_id' => \App\Models\Theme::inRandomOrder()->first()?->id ?? \App\Models\Theme::factory(),
            'topic_id' => \App\Models\Topic::inRandomOrder()->first()?->id ?? \App\Models\Topic::factory(),
            'national_priority_id' => \App\Models\NationalPriority::inRandomOrder()->first()?->id ?? \App\Models\NationalPriority::factory(),
            'cluster_level1_id' => null,
            'cluster_level2_id' => null,
            'cluster_level3_id' => null,
            'sbk_value' => fake()->randomFloat(2, 5000000, 50000000),
            'duration_in_years' => fake()->numberBetween(1, 3),
            'start_year' => (int) date('Y'),
            'summary' => fake()->paragraphs(3, true),
            'status' => fake()->randomElement(\App\Enums\ProposalStatus::values()),
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterMaking(function (\App\Models\Proposal $proposal) {
            if (! $proposal->cluster_level3_id) {
                $cluster3 = \App\Models\ScienceCluster::where('level', 3)->inRandomOrder()->first();
                if ($cluster3) {
                    $proposal->cluster_level3_id = $cluster3->id;
                    $proposal->cluster_level2_id = $cluster3->parent_id;
                    
                    $cluster2 = \App\Models\ScienceCluster::find($cluster3->parent_id);
                    if ($cluster2) {
                        $proposal->cluster_level1_id = $cluster2->parent_id;
                    }
                } else {
                    // Fallback to level 1 if no level 3 found
                    $cluster1 = \App\Models\ScienceCluster::where('level', 1)->inRandomOrder()->first();
                    $proposal->cluster_level1_id = $cluster1?->id;
                }
            }
        });
    }
}
