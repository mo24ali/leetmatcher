<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BlogPost>
 */
class BlogPostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'author_id' => \App\Models\User::factory(),
            'title' => fake()->sentence(),
            'body' => fake()->paragraphs(5, true),
            'status' => 'published',
            'visibility' => 'public',
            'tags' => ['tech', 'career', 'matching'],
            'moderation_status' => 'approved',
        ];
    }
}
