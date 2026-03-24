<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\Skill;
use Illuminate\Http\Request;
use Smalot\PdfParser\Parser;

class ProfileController extends Controller
{


    public function uploadCv(Request $request)
    {
        $request->validate([
            'cv' => 'required|mimes:pdf|max:2048'
        ]);

        $file = $request->file('cv');

        $path = $file->store('cvs');

        $parser = new Parser();
        $pdf = $parser->parseFile(storage_path('app/' . $path));
        $text = strtolower($pdf->getText());

        $skills = $this->extractSkills($text);

        $user = $request->user();
        $profile = $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            ['cv_path' => $path]
        );
        // The previous line was likely failing or not doing anything persistent if column 'skills' didn't exist
        // I'll keep the profile updated with the score logic but migrate storage to the skills table
        
        // Sync skills: for simplicity delete existing ones and add new ones (normalized)
        $user->skills()->delete();
        
        foreach ($skills as $skillName) {
            $user->skills()->create([
                'name' => $skillName,
                'proficiency' => 'intermediate', // Default or extracted if possible
            ]);
        }

        $profile->cv_score = $this->calculateScore($profile);
        $profile->save();

        return response()->json([
            'skills' => $user->skills()->get(),
            'score' => $profile->cv_score
        ]);
    }

    public function getSkills(Request $request)
    {
        return response()->json([
            'skills' => $request->user()->skills
        ]);
    }
    private function extractSkills($text)
    {
        $knownSkills = ['php', 'laravel', 'vue', 'react', 'mysql', 'js'];

        $found = [];

        foreach ($knownSkills as $skill) {
            if (str_contains($text, $skill)) {
                $found[] = $skill;
            }
        }

        return $found;
    }

    private function calculateScore($profile)
    {
        $score = 0;

        if ($profile->user->skills()->exists()) $score += 40;
        if ($profile->cv_path) $score += 30;
        if ($profile->bio) $score += 30;

        return $score;
    }
}
