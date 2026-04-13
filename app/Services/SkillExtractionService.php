<?php

namespace App\Services;

use App\Models\Skill;
use App\Models\Project;
use App\Models\Profile;
use Illuminate\Support\Str;

class SkillExtractionService
{
    /**
     * Common technical skill synonyms/variations.
     */
    protected $synonyms = [
        'js' => 'JavaScript',
        'node' => 'Node.js',
        'vue' => 'Vue.js',
        'reactjs' => 'React',
        'postgresql' => 'PostgreSQL',
        'postgres' => 'PostgreSQL',
        'mysql' => 'MySQL',
        'laravel' => 'Laravel',
        'docker' => 'Docker',
        'k8s' => 'Kubernetes',
        'aws' => 'AWS',
        'ts' => 'TypeScript',
        '.net' => 'C#',
        'flutter' => 'Flutter',
        'git' => 'Git',
        'github' => 'Git',
    ];

    /**
     * 1. Skills Table: Ensure skills exist without duplication.
     * This method takes raw skill names and ensures they are in the 'skills' table.
     */
    public function ensureSkillsExist(array $skillNames): \Illuminate\Support\Collection
    {
        $skillModels = collect();

        foreach ($skillNames as $name) {
            $name = trim($name);
            if (empty($name)) continue;

            // Canonicalize name check (case-insensitive)
            $skill = Skill::whereRaw('LOWER(name) = ?', [strtolower($name)])->first();

            if (!$skill) {
                // Check synonyms before creating new
                $canonicalName = $this->synonyms[strtolower($name)] ?? $name;
                $skill = Skill::firstOrCreate(
                    ['name' => $canonicalName]
                );
            }

            $skillModels->push($skill);
        }

        return $skillModels->unique('id');
    }

    /**
     * Extract skills from a given text by matching against ALL known skills.
     */
    public function extract(string $text)
    {
        // Avoid running queries and loading Eloquent models into memory continuously.
        // Retrieve just ids and names map.
        $skills = Skill::pluck('id', 'name')->toArray();
        $extractedSkillIds = collect();

        // Check for existing skills
        foreach ($skills as $dbName => $id) {
            $pattern = '/(?<![a-zA-Z0-9])' . preg_quote(strtolower($dbName), '/') . '(?![a-zA-Z0-9])/i';
            if (preg_match($pattern, $text)) {
                $extractedSkillIds->push($id);
            }
        }

        // Check for synonyms
        foreach ($this->synonyms as $synonym => $canonical) {
            if (isset($skills[$canonical])) { // If the canonical skill is in the DB
                $synPattern = '/(?<![a-zA-Z0-9])' . preg_quote(strtolower($synonym), '/') . '(?![a-zA-Z0-9])/i';
                if (preg_match($synPattern, $text)) {
                    $extractedSkillIds->push($skills[$canonical]);
                }
            }
        }

        // Return the Eloquent models associated with uniquely matched IDs
        return Skill::whereIn('id', $extractedSkillIds->unique())->get();
    }

    /**
     * 2. Profile Skills Table: Stores relationship between user profile and extracted skills.
     */
    public function syncToProfile(Profile $profile, array $skillNames)
    {
        // Step 1: Ensure skills exist in the canonical 'skills' table
        $skills = $this->ensureSkillsExist($skillNames);

        // Step 2: Store relationship in 'profile_skills'
        $syncData = [];
        foreach ($skills as $skill) {
            $syncData[$skill->id] = ['proficiency' => 'intermediate'];
        }

        $profile->skills()->sync($syncData);
    }

    /**
     * 3. Project Skills Table: Stores skills for a specific project.
     */
    public function syncToProject(Project $project, array $skillNames = null)
    {
        if ($skillNames === null) {
            // Auto-extract if no manual skills provided
            $skills = $this->extract($project->description . ' ' . $project->title);
        } else {
            // Step 1: Ensure skills exist in canonical table
            $skills = $this->ensureSkillsExist($skillNames);
        }

        // Step 3: Store relationship in 'project_skills'
        $syncData = [];
        foreach ($skills as $skill) {
            $syncData[$skill->id] = ['level' => 'intermediate'];
        }

        $project->skills()->sync($syncData);
    }
}
