<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Skill>
 */
class SkillsFactory extends Factory
{
    protected $model = \App\Models\Skill::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'PHP', 'Laravel', 'React', 'Vue', 'Docker', 'AWS', 'Python', 'Node.js', 
                'Tailwind CSS', 'SQL', 'PostgreSQL', 'Redis', 'Unit Testing', 'CI/CD',
                'Go', 'Ruby on Rails', 'Flutter', 'Express', 'Angular', 'Svelte'
            ]),
        ];
    }
}
