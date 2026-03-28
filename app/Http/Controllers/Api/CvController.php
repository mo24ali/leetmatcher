<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CvController extends Controller
{
    public function upload(Request $request)
    {

        $request->validate([
            'cv' => 'required|file|mimes:pdf,doc,docx|max:5120',
        ]);

        $file = $request->file('cv');
        $path = $file->store('cvs', 'local');
        $parsed = $this->parseFile($file);


        $user = $request->user();
        $profile = $user->profile()->updateOrCreate(
        ['user_id' => $user->id],
        ['cv_path' => $path]
        );

        if (isset($parsed['skills'])) {
            $user->skills()->delete();
            foreach ($parsed['skills'] as $skillName) {
                $user->skills()->create([
                    'name' => $skillName,
                    'proficiency' => 'intermediate'
                ]);
            }
        }

        // Recalculate cv_score (calling logic similar to ProfileController)
        $profile->cv_score = $this->calculateScore($profile);
        $profile->save();

        return response()->json([
            'message' => 'CV uploaded and parsed successfully',
            'path' => $path,
            'parsed' => $parsed,
            'skills' => $user->skills,
            'score' => $profile->cv_score
        ]);
    }

    private function calculateScore($profile)
    {
        $score = 0;
        if ($profile->user->skills()->exists())
            $score += 40;
        if ($profile->cv_path)
            $score += 30;
        if ($profile->bio)
            $score += 30;
        return $score;
    }

    private function parseFile($file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $text = '';

        if ($extension === 'pdf') {
            $text = $this->extractFromPdf($file->getRealPath());
        }
        elseif (in_array($extension, ['doc', 'docx'])) {
            $text = $this->extractFromDocx($file->getRealPath());
        }

        return $this->extractFields($text);
    }

    private function extractFromPdf(string $path): string
    {
        // Use pdftotext if available on the system, otherwise fall back gracefully
        if (shell_exec('which pdftotext 2>/dev/null')) {
            $escaped = escapeshellarg($path);
            return shell_exec("pdftotext {$escaped} - 2>/dev/null") ?? '';
        }

        // Naive binary scan for readable text (best-effort for plain PDFs)
        $content = file_get_contents($path);
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
        // DOCX is a ZIP archive; word/document.xml holds the text
        if (!class_exists('ZipArchive')) {
            return '';
        }

        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) {
            return '';
        }

        $xml = $zip->getFromName('word/document.xml');
        $zip->close();

        if (!$xml) {
            return '';
        }

        // Strip XML tags, decode entities
        $text = strip_tags(str_replace('</w:p>', "\n", $xml));
        return html_entity_decode($text);
    }

    private function extractFields(string $text): array
    {
        $lines = array_filter(array_map('trim', explode("\n", $text)));

        $name = '';
        foreach (array_slice(array_values($lines), 0, 5) as $line) {
            if (preg_match('/^[A-Z][a-z]+([ \'][A-Z][a-z]+)+$/', $line)) {
                $name = $line;
                break;
            }
        }

        $email = '';
        preg_match('/[\w.+-]+@[\w-]+\.[a-zA-Z]{2,}/', $text, $emailMatch);
        if ($emailMatch) {
            $email = $emailMatch[0];
        }

        $phone = '';
        preg_match('/(\+?\d[\d\s\-().]{7,}\d)/', $text, $phoneMatch);
        if ($phoneMatch) {
            $phone = trim($phoneMatch[1]);
        }

        // --- Skills ---
        $skillsPatterns = ['skills?', 'compétences?\s+techniques', 'compétences?', 'outils', 'langages', 'savoir-faire', 'technologies', 'téchnologies'];
        $breakPatterns = [
            'education', 'experience', 'summary', 'formation', 'expériences?\s+professionnelles',
            'expériences?', 'études', 'diplômes', 'parcours\s+professionnel', 'parcours', 'résumé'
        ];

        $skillsRegex = '/(?:' . implode('|', $skillsPatterns) . ')[:\s]+([^\n]+(?:\n(?!(?:' . implode('|', $breakPatterns) . '))[^\n]+)*)/i';

        $skills = [];
        if (preg_match($skillsRegex, $text, $skillsMatch)) {
            $raw = $skillsMatch[1];
            $sections = preg_split('/[,|•\n\t]+/', $raw);
            $skills = array_values(array_unique(array_filter(
                array_map('trim', $sections),
            fn($s) => strlen($s) > 1 && strlen($s) < 40
            )));
            $skills = array_slice($skills, 0, 15);
        }

        // --- Education ---
        $eduPatterns = ['education', 'formation', 'études', 'diplômes'];
        $eduRegex = '/(?:' . implode('|', $eduPatterns) . ')[:\s]+((?:[^\n]+\n?){1,6})/i';
        $education = [];
        if (preg_match($eduRegex, $text, $eduMatch)) {
            $raw = $eduMatch[1];
            $education = array_values(array_filter(
                explode("\n", $raw),
            fn($l) => strlen(trim($l)) > 3
            ));
            $education = array_map('trim', array_slice($education, 0, 4));
        }

        // --- Experience ---
        $expPatterns = ['experience', 'expériences?\s+professionnelles', 'expériences?', 'parcours\s+professionnel', 'parcours'];
        $expRegex = '/(?:' . implode('|', $expPatterns) . ')[:\s]+((?:[^\n]+\n?){1,10})/i';
        $experience = [];
        if (preg_match($expRegex, $text, $expMatch)) {
            $raw = $expMatch[1];
            $experience = array_values(array_filter(
                explode("\n", $raw),
            fn($l) => strlen(trim($l)) > 3
            ));
            $experience = array_map('trim', array_slice($experience, 0, 6));
        }

        return compact('name', 'email', 'phone', 'skills', 'education', 'experience');
    }
}