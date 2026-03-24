<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Illuminate\Foundation\Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;
use App\Models\Interview;
use App\Models\Profile;
use App\Services\MatchingService;
use Illuminate\Support\Facades\Log;

Schedule::call(function () {
    $interviews = Interview::where('scheduled_at', '>=', now())
        ->where('scheduled_at', '<=', now()->addHours(24))
        ->get();
        
    foreach ($interviews as $interview) {
        Log::info("Sending interview reminder to " . $interview->application->student->email . " for interview on " . $interview->scheduled_at);
    }
})->daily();

Schedule::call(function (MatchingService $matchingService) {
    $profiles = Profile::with('user')->get();
    foreach ($profiles as $profile) {
        $recommendations = $matchingService->getRecommendedProjects($profile);
        if ($recommendations->isNotEmpty()) {
            Log::info("Sending weekly project suggestions to " . $profile->user->email . " with " . $recommendations->count() . " matches.");
        }
    }
})->weekly();
