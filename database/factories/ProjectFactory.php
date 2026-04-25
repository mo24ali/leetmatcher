<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'recruiter_id' => \App\Models\User::factory()->recruiter(),
            'title' => fake()->jobTitle(),
            'description' => fake()->paragraphs(3, true),
            'deadline' => fake()->dateTimeBetween('+1 week', '+2 months'),
            'status' => fake()->randomElement(['open', 'closed']),
        ];
    }
}
