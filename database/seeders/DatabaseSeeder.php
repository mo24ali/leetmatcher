<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SkillsSeeder::class,
            UserSeeder::class,
            ProjectSeeder::class,
            ApplicationSeeder::class,
            InterviewSeeder::class,
            MessageSeeder::class,
            NotificationSeeder::class,
            ReviewSeeder::class,
            BlogPostSeeder::class,
            AuditLogSeeder::class,
            ModerationActionSeeder::class,
        ]);
    }
}
