<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // One default admin for testing
        \App\Models\User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@leetmatcher.com',
        ]);

        // Five recruiters with profiles
        \App\Models\User::factory()->count(5)->recruiter()->create()->each(function ($user) {
            \App\Models\Profile::factory()->create(['user_id' => $user->id]);
        });

        // Ten applicants with profiles
        \App\Models\User::factory()->count(10)->applicant()->create()->each(function ($user) {
            \App\Models\Profile::factory()->create(['user_id' => $user->id]);
        });
    }
}
