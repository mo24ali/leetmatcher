<?php

namespace App\Services;

use App\Models\Profile;

class CvScoringService
{
    /**
     * Calculate profile completeness score.
     */
    public function calculateScore(Profile $profile): int
    {
        $score = 0;

        if ($profile->bio) $score += 15; // profile with bio +15%
        if ($profile->avatar_url) $score += 15; // profile with avatar +15%
        if ($profile->cv_path) $score += 20; // profile with cv_path importes +15%
        if ($profile->skills()->count() >= 3) $score += 20; // profile with skills->count >= 3 +20%
        elseif ($profile->skills()->count() > 0) $score += 10; // profile with skills->count < 3 > 0 + 10%
        if ($profile->skills()->count() > 5) $score += 30; // profile with skills->count > 5 +30%
        elseif ($profile->skills()->count() > 2) $score += 15; // profile with skills->count > 2 +15%

        $finalScore = min(100, $score);
        
        $profile->update(['cv_score' => $finalScore]);
        
        return $finalScore;
    }
}
