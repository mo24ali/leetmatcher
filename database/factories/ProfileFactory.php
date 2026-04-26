<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Profile>
 */
class ProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'bio' => fake()->paragraph(),
            'avatar_url' => fake()->imageUrl(200, 200, 'people'),
            'cv_path' => 'cvs/dummy_cv.pdf',
            'cv_score' => fake()->numberBetween(0, 100),
        ];
    }
}
