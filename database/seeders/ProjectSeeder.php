<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $recruiters = \App\Models\User::where('role', 'recruiter')->get();
        $skills = \App\Models\Skill::all();

        if ($recruiters->isEmpty()) {
            $recruiters = \App\Models\User::factory()->count(3)->recruiter()->create();
        }

        \App\Models\Project::factory()->count(15)->create([
            'recruiter_id' => fn() => $recruiters->random()->id
        ])->each(function ($project) use ($skills) {
            // Assign 3-6 random skills to each project
            $randomSkills = $skills->random(rand(3, 6));
            foreach ($randomSkills as $skill) {
                $project->skills()->attach($skill->id, [
                    'level' => fake()->randomElement(['junior', 'intermediate', 'senior'])
                ]);
            }
        });
    }
}
