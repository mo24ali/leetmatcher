<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ModerationAction>
 */
class ModerationActionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory()->applicant(),
            'admin_id' => \App\Models\User::factory()->admin(),
            'type' => fake()->randomElement(['spam', 'terms_violation', 'harassment']),
            'reason' => fake()->sentence(),
            'level' => fake()->numberBetween(1, 5),
        ];
    }
}
