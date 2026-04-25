<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BlogPostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = \App\Models\User::all();

        if ($users->isEmpty()) {
            return;
        }

        \App\Models\BlogPost::factory()->count(12)->create([
            'author_id' => fn() => $users->random()->id
        ]);
    }
}
