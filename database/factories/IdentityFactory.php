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
        $type = fake()->randomElement(['dosen', 'mahasiswa']);

        return [
            'identity_id' => $type === 'dosen'
                ? fake()->numerify('##########') // NIDN 10 digits
                : fake()->numerify('################'), // NIM 16 digits
            'sinta_id' => $type === 'dosen'
                ? fake()->optional(0.7)->numerify('######')
                : null,
            'type' => $type,
            'user_id' => User::factory(),
            'institution_id' => \App\Models\Institution::factory(),
            'study_program_id' => \App\Models\StudyProgram::factory(),
            'faculty_id' => \App\Models\Faculty::factory(),
            'address' => $this->faker->address(),
            'birthdate' => $this->faker->date(),
            'birthplace' => $this->faker->city(),
            'profile_picture' => $this->faker->imageUrl(300, 300, 'people'),
        ];
    }
}
