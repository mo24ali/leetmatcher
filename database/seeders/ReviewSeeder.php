<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = \App\Models\User::all();

        if ($users->count() < 2) {
            return;
        }

        \App\Models\Review::factory()->count(15)->create([
            'reviewer_id' => fn() => $users->random()->id,
            'reviewed_user_id' => fn() => $users->random()->id,
        ]);
    }
}
