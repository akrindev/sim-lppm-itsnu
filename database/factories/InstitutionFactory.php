<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Institution>
 */
class InstitutionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $institutions = [
            'Institut Teknologi dan Sains Nahdlatul Ulama Pekalongan',
            'Universitas Islam Negeri Walisongo Semarang',
            'Universitas Diponegoro',
            'Institut Teknologi Bandung',
            'Universitas Gadjah Mada',
            'Universitas Indonesia',
            'Institut Teknologi Sepuluh Nopember',
            'Universitas Airlangga',
            'Universitas Brawijaya',
            'Universitas Sebelas Maret',
        ];

        return [
            'name' => fake()->unique()->randomElement($institutions),
        ];
    }
}
