<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ModerationAction;

class ModerationActionSeeder extends Seeder
{
    public function run(): void
    {
        ModerationAction::factory()->count(12)->create();
    }
}
