<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SkillsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $skills = [
            // Languages
            'PHP', 'JavaScript', 'TypeScript', 'Python', 'Java', 'C#', 'C++', 'Ruby', 'Go', 'Rust', 'Swift', 'Kotlin',
            // Frontend
            'React', 'Vue.js', 'Angular', 'Svelte', 'Next.js', 'Nuxt.js', 'Tailwind CSS', 'Bootstrap', 'HTML5', 'CSS3',
            // Backend / Frameworks
            'Laravel', 'Node.js', 'Express.js', 'Spring Boot', 'Django', 'Flask', 'Rails', 'ASP.NET Core',
            // Databases
            'MySQL', 'PostgreSQL', 'MongoDB', 'Redis', 'SQLite', 'Oracle', 'SQL Server', 'Firebase',
            // DevOps / Cloud
            'Docker', 'Kubernetes', 'AWS', 'Google Cloud', 'Azure', 'Git', 'CI/CD', 'Terraform', 'Nginx',
            // Specialized
            'Machine Learning', 'AI', 'Data Science', 'Blockchain', 'Cybersecurity', 'REST API', 'GraphQL', 'WebSockets',
            'Unit Testing', 'TDD', 'Agile', 'Scrum', 'Linux'
        ];

        foreach ($skills as $skillName) {
            \App\Models\Skill::firstOrCreate([
                'name' => $skillName
            ]);
        }
    }
}
