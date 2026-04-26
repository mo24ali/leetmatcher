<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AuditLog>
 */
class AuditLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory()->admin(),
            'event_type' => fake()->randomElement(['USER_DELETED', 'USER_WARNED', 'PROJECT_CREATED', 'LOGIN']),
            'severity' => fake()->randomElement(['info', 'warning', 'error']),
            'metadata' => ['ip' => fake()->ipv4(), 'user_agent' => fake()->userAgent()],
        ];
    }
}
