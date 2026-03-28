<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Get the authenticated user's skills.
     */
    public function getSkills(Request $request)
    {
        return response()->json([
            'skills' => $request->user()->skills
        ]);
    }
}
