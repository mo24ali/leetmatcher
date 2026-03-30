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

        // Base profile info (30 pts)
        if ($profile->bio) $score += 15;
        if ($profile->avatar_url) $score += 15;

        // Professional data (40 pts)
        if ($profile->cv_path) $score += 20;
        if ($profile->skills()->count() >= 3) $score += 20;
        elseif ($profile->skills()->count() > 0) $score += 10;

        // Experience/Education proxy (30 pts)
        // If they have skills we assume some level of expertise/education for basic score
        if ($profile->skills()->count() > 5) $score += 30;
        elseif ($profile->skills()->count() > 2) $score += 15;

        $finalScore = min(100, $score);
        
        $profile->update(['cv_score' => $finalScore]);
        
        return $finalScore;
    }
}
