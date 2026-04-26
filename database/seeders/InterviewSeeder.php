<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InterviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $applications = \App\Models\Application::all();

        if ($applications->isEmpty()) {
            return;
        }

        // Use random applications but ensure unique application_id if there's a constraint (usually one interview per application)
        // Even if no constraint, it makes sense.
        $selectedApplications = $applications->shuffle()->take(10);

        foreach ($selectedApplications as $app) {
            \App\Models\Interview::factory()->create([
                'application_id' => $app->id,
            ]);
        }
    }
}
