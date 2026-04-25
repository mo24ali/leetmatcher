<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
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

        for ($i = 0; $i < 30; $i++) {
            $sender = $users->random();
            $receiver = $users->where('id', '!=', $sender->id)->random();

            \App\Models\Message::factory()->create([
                'sender_id' => $sender->id,
                'receiver_id' => $receiver->id,
            ]);
        }
    }
}
