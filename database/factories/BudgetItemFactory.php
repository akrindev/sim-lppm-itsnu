<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BudgetItem>
 */
class BudgetItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $volume = fake()->numberBetween(1, 20);
        $unitPrice = fake()->randomFloat(2, 50000, 5000000);

        $group = \App\Models\BudgetGroup::inRandomOrder()->first();
        $component = $group
            ? \App\Models\BudgetComponent::where('budget_group_id', $group->id)->inRandomOrder()->first()
            : null;

        return [
            'proposal_id' => \App\Models\Proposal::factory(),
            'year' => 1,
            'budget_group_id' => $group?->id,
            'budget_component_id' => $component?->id,
            'group' => $group?->name ?? fake()->word(),
            'component' => $component?->name ?? fake()->word(),
            'item_description' => fake()->sentence(4),
            'volume' => $volume,
            'unit_price' => $unitPrice,
            'total_price' => $volume * $unitPrice,
        ];
    }
}
