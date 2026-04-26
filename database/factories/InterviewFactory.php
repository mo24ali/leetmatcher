<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Interview>
 */
class InterviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'application_id' => \App\Models\Application::factory(),
            'scheduled_at' => fake()->dateTimeBetween('now', '+1 month'),
            'meeting_link' => fake()->url(),
            'notes' => fake()->sentence(),
            'score' => fake()->numberBetween(0, 100),
            'status' => fake()->randomElement(['scheduled', 'completed']),
        ];
    }
}
