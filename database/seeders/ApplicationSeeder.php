<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $applicants = \App\Models\User::where('role', 'applicant')->get();
        $projects = \App\Models\Project::all();

        if ($applicants->isEmpty() || $projects->isEmpty()) {
            return;
        }

        $usedPairs = [];
        $count = 0;
        $maxAttempts = 50;

        while ($count < 20 && $maxAttempts > 0) {
            $studentId = $applicants->random()->id;
            $projectId = $projects->random()->id;
            $pair = "{$studentId}-{$projectId}";

            if (!isset($usedPairs[$pair])) {
                \App\Models\Application::factory()->create([
                    'student_id' => $studentId,
                    'project_id' => $projectId,
                ]);
                $usedPairs[$pair] = true;
                $count++;
            }
            $maxAttempts--;
        }
    }
}
