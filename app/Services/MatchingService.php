<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Profile;

class MatchingService
{
    /**
     * Calculate the match score between a project and an applicant profile.
     * Returns a percentage score (0 to 100).
     */
    public function calculateMatch(Project $project, Profile $profile): float
    {
        $projectSkills = $project->skills()->pluck('skills.id')->toArray();
        $profileSkills = $profile->skills()->pluck('skills.id')->toArray();

        if (empty($projectSkills)) {
            return 100.0; // No requirements means anyone is a match
        }

        $intersection = array_intersect($projectSkills, $profileSkills);
        // the matching scire is caldulated by dividing the count on intersections by the count of skills demamnded from the project
        // the division is multiplied by 100
        $score = (count($intersection) / count($projectSkills)) * 100;

        return round($score, 2);
    }

    /**
     * Get a list of recommended projects for a given profile.
     */
    public function getRecommendedProjects(Profile $profile, int $limit = 5)
    {
        // This is a simplified recommendation engine
        $projects = Project::where('status', 'open')->with('skills')->get();

        $recommendations = $projects->map(function ($project) use ($profile) {
            $project->match_score = $this->calculateMatch($project, $profile);
            return $project;
        })
        ->filter(fn($project) => $project->match_score > 0)
        ->sortByDesc('match_score')
        ->values()
        ->take($limit);

        return $recommendations;
    }
    public function getRecommendedCandidates(Project $project, int $limit = 10)
    {
        $profiles = Profile::with(['skills'])->get();

        
        $recommendations = $profiles->map(function($profile) use ($project){
            $profile->match_score = $this->calculateMatch($project, $profile);
            return $profile;
        })
        ->filter(fn($profile) => $profile->match_score > 0)
        ->sortByDesc('match_score')
        ->values()
        ->take($limit);

        return $recommendations;
    }

    /**
     * Get candidates matching ANY of a recruiter's open projects.
     */
    public function getRecruiterMatches($recruiter, int $limit = 10)
    {
        $projects = Project::where('recruiter_id', $recruiter->id)
            ->where('status', 'open')
            ->with('skills')
            ->get();

        if ($projects->isEmpty()) {
            return collect();
        }

        $profiles = Profile::with(['user', 'skills'])->get();

        return $profiles->map(function ($profile) use ($projects) {
            // Take the max score across all projects
            $bestScore = 0;
            foreach ($projects as $project) {
                $score = $this->calculateMatch($project, $profile);
                if ($score > $bestScore) $bestScore = $score;
            }
            $profile->match_score = $bestScore;
            return $profile;
        })
        ->filter(fn($profile) => $profile->match_score > 0)
        ->sortByDesc('match_score')
        ->values()
        ->take($limit);
    }
}
