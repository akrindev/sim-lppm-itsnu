<?php

namespace Database\Factories;

use App\Models\Identity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class IdentityFactory extends Factory
{
    protected $model = Identity::class;

    public function definition(): array
    {
        return [
            'identity_id' => $this->faker->unique()->numerify('ID#####'),
            'user_id' => User::factory(),
            'address' => $this->faker->address(),
            'birthdate' => $this->faker->date(),
            'birthplace' => $this->faker->city(),
            'profile_picture' => $this->faker->imageUrl(300, 300, 'people'),
        ];
    }
}
