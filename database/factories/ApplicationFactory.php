<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Application>
 */
class ApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => \App\Models\User::factory()->applicant(),
            'project_id' => \App\Models\Project::factory(),
            'cover_letter' => fake()->paragraphs(2, true),
            'status' => fake()->randomElement(['pending', 'in_progress', 'accepted', 'rejected']),
        ];
    }
}
