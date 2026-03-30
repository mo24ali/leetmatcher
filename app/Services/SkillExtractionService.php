<?php

namespace App\Services;

use App\Models\Skill;
use App\Models\Project;
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
     * Extract skills from a given text (e.g., project description).
     *
     * @param string $text
     * @return \Illuminate\Support\Collection
     */
    public function extract(string $text)
    {
        // Get all canonical skill names from database
        $allSkills = Skill::all();
        $textLower = strtolower($text);

        $extractedSkills = collect();

        foreach ($allSkills as $skill) {
            $skillName = strtolower($skill->name);
            
            // Exact match (using word boundaries)
            $pattern = '/\b' . preg_quote($skillName, '/') . '\b/i';
            
            if (preg_match($pattern, $text)) {
                $extractedSkills->push($skill);
                continue;
            }

            // Check synonyms
            foreach ($this->synonyms as $synonym => $canonical) {
                if ($canonical === $skill->name) {
                    $synPattern = '/\b' . preg_quote($synonym, '/') . '\b/i';
                    if (preg_match($synPattern, $text)) {
                        $extractedSkills->push($skill);
                        break;
                    }
                }
            }
        }

        return $extractedSkills->unique('id');
    }

    /**
     * Sync extracted skills to a project.
     *
     * @param Project $project
     * @return void
     */
    public function syncToProject(Project $project)
    {
        $skills = $this->extract($project->description . ' ' . $project->title);
        
        $syncData = [];
        foreach ($skills as $skill) {
            $syncData[$skill->id] = ['level' => 'intermediate'];
        }

        $project->skills()->sync($syncData);
    }
}
