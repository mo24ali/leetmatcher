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
        $projects = Project::where('status', 'open')
            ->with(['skills', 'recruiter.profile', 'recruiter.reviewsReceived.reviewer'])
            ->get();

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
        $projectSkillIds = $project->skills()->pluck('skills.id')->toArray();
        $totalRequired = count($projectSkillIds);

        if ($totalRequired === 0) {
            // If project demands 0 skills, return limits mapped to 100%
            return Profile::with(['skills'])
                ->limit($limit)
                ->get()
                ->map(function($profile) {
                    $profile->match_score = 100.0;
                    return $profile;
                });
        }

        // SQL approach:
        // Join with profile_skills to match against project skills and calculate score directly there.
        $profiles = Profile::selectRaw('profiles.*, ROUND((COUNT(profile_skills.skill_id) / ?) * 100, 2) as match_score', [$totalRequired])
            ->join('profile_skills', 'profiles.id', '=', 'profile_skills.profile_id')
            ->whereIn('profile_skills.skill_id', $projectSkillIds)
            ->with(['skills', 'user']) // eager load relevant relationships
            ->groupBy('profiles.id')
            ->orderByDesc('match_score')
            ->limit($limit)
            ->get();

        return $profiles;
    }

    /**
     * Get candidates matching ANY of a recruiter's open projects.
     */
    public function getRecruiterMatches($recruiter, int $limit = 10)
    {
        // First get all skill ids over all open projects for this recruiter
        $projectSkillIds = \Illuminate\Support\Facades\DB::table('project_skills')
            ->join('projects', 'project_skills.project_id', '=', 'projects.id')
            ->where('projects.recruiter_id', $recruiter->id)
            ->where('projects.status', 'open')
            ->pluck('project_skills.skill_id')
            ->unique()
            ->toArray();

        if (empty($projectSkillIds)) {
            return collect();
        }

        // To calculate max score across *multiple projects* with different demands is tricky purely in SQL
        // without complex subqueries. But a highly robust approach for thousands of users is to fetch candidates
        // that share AT LEAST ONE skill broadly, then score them against individual projects in memory.
        // We avoid fetching ALL profiles across the DB, we only query those that partially match.

        $profiles = Profile::whereHas('skills', function($q) use ($projectSkillIds) {
            $q->whereIn('skills.id', $projectSkillIds);
        })->with(['user.reviewsReceived.reviewer', 'skills'])->get();

        $projects = Project::where('recruiter_id', $recruiter->id)
            ->where('status', 'open')
            ->with('skills')
            ->get();

        return $profiles->map(function ($profile) use ($projects) {
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
