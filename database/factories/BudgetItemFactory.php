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

        return [
            'proposal_id' => \App\Models\Proposal::factory(),
            'group' => fake()->randomElement([
                'Honor',
                'Bahan Habis Pakai',
                'Peralatan',
                'Perjalanan',
                'Lain-lain',
            ]),
            'component' => fake()->randomElement([
                'Honor Ketua',
                'Honor Anggota',
                'ATK',
                'Bahan Penelitian',
                'Laptop',
                'Printer',
                'Transportasi Lokal',
                'Akomodasi',
            ]),
            'item_description' => fake()->sentence(4),
            'volume' => $volume,
            'unit_price' => $unitPrice,
            'total_price' => $volume * $unitPrice,
        ];
    }
}
