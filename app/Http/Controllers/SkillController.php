<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Skill;
class SkillController extends Controller
{
    //


    /**
     * Search skills for autocomplete.
     */
    public function search(Request $request)
    {
        $q = $request->query('query', '');
        if (strlen($q) < 2) return response()->json([]);

        $skills = Skill::where('name', 'LIKE', "%{$q}%")
            ->limit(10)
            ->pluck('name')
            ->unique()
            ->values();

        return response()->json($skills);
    }
}
