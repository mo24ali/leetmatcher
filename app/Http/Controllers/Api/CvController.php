<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CvController extends Controller
{
    protected $scoringService;
    protected $skillExtractor;

    public function __construct(\App\Services\CvScoringService $scoringService, \App\Services\SkillExtractionService $skillExtractor)
    {
        $this->scoringService = $scoringService;
        $this->skillExtractor = $skillExtractor;
    }

    public function upload(Request $request)
    {
        $request->validate([
            'cv' => 'required|file|mimes:pdf,doc,docx|max:5120',
        ]);

        $file = $request->file('cv');
        $path = $file->store('cvs', 'local');
        
        $text = $this->extractText($file);
        $parsed = $this->extractFields($text);

        // Get matching skills from database
        $extractedSkills = $this->skillExtractor->extract($text);
        
        $user = $request->user();
        $profile = $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            ['cv_path' => $path]
        );

        // Sync skills to profile pivot
        $syncData = [];
        foreach ($extractedSkills as $skill) {
            $syncData[$skill->id] = ['proficiency' => 'intermediate'];
        }
        $profile->skills()->sync($syncData);

        // Calculate score
        $score = $this->scoringService->calculateScore($profile);
        $profile->update(['cv_score' => $score]);

        return response()->json([
            'message' => 'CV uploaded and parsed successfully',
            'path'    => $path,
            'parsed'  => $parsed,
            'skills'  => $profile->skills()->get()->map(fn($s) => [
                'id' => $s->id,
                'name' => $s->name,
                'proficiency' => $s->pivot?->proficiency ?? 'intermediate',
            ]),
            'score'   => $score
        ]);
    }

    private function extractText($file): string
    {
        $extension = $file->getClientOriginalExtension();
        $path = $file->getRealPath();

        if ($extension === 'pdf') {
            return $this->extractFromPdf($path);
        } elseif (in_array($extension, ['doc', 'docx'])) {
            return $this->extractFromDocx($path);
        }

        return '';
    }

    private function extractFromPdf(string $path): string
    {
        if (shell_exec('which pdftotext 2>/dev/null')) {
            $escaped = escapeshellarg($path);
            return shell_exec("pdftotext {$escaped} - 2>/dev/null") ?? '';
        }

        $content = @file_get_contents($path);
        if (!$content) return '';
        
        $text = '';
        preg_match_all('/BT (.+?) ET/s', $content, $matches);
        foreach ($matches[1] as $block) {
            preg_match_all('/\((.*?)\)\s*Tj/', $block, $parts);
            $text .= implode(' ', $parts[1]) . ' ';
        }
        return $text;
    }

    private function extractFromDocx(string $path): string
    {
        if (!class_exists('ZipArchive')) return '';

        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) return '';

        $xml = $zip->getFromName('word/document.xml');
        $zip->close();

        if (!$xml) return '';

        $text = strip_tags(str_replace('</w:p>', "\n", $xml));
        return html_entity_decode($text);
    }

    private function extractFields(string $text): array
    {
        $lines = array_filter(array_map('trim', explode("\n", $text)));

        $name = '';
        foreach (array_slice($lines, 0, 5) as $line) {
            if (preg_match('/^[A-Z][a-z]+([ \'][A-Z][a-z]+)+$/', $line)) {
                $name = $line;
                break;
            }
        }

        $email = '';
        preg_match('/[\w.+-]+@[\w-]+\.[a-zA-Z]{2,}/', $text, $emailMatch);
        $email = $emailMatch[0] ?? '';

        $phone = '';
        preg_match('/(\+?\d[\d\s\-().]{7,}\d)/', $text, $phoneMatch);
        $phone = trim($phoneMatch[1] ?? '');

        // Basic sections
        $skills = $this->grepSection($text, ['skills', 'compétences', 'outils', 'technologies']);
        $education = $this->grepSection($text, ['education', 'formation', 'études']);
        $experience = $this->grepSection($text, ['experience', 'expériences', 'parcours']);

        return compact('name', 'email', 'phone', 'skills', 'education', 'experience');
    }

    private function grepSection(string $text, array $keywords): array
    {
        $pattern = '/(?:' . implode('|', $keywords) . ')[:\s]+([^\n]+(?:\n(?!(?:education|experience|formation|études|skills|compétences))[^\n]+)*)/i';
        
        if (preg_match($pattern, $text, $matches)) {
            $sections = preg_split('/[,|•\n\t]+/', $matches[1]);
            return array_values(array_unique(array_filter(
                array_map('trim', $sections),
                fn($s) => strlen($s) > 1 && strlen($s) < 100
            )));
        }
        return [];
    }
}