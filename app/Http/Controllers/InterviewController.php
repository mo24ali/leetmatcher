<?php

namespace App\Http\Controllers;

use App\Models\Interview;
use Illuminate\Http\Request;

class InterviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Interview::class);

        $user = auth()->user();
        $query = Interview::query();

        if ($user->role === 'recruiter') {
            $query->whereHas('application.project', function ($q) use ($user) {
                $q->where('recruiter_id', $user->id);
            });
        } elseif ($user->role === 'student' || $user->role === 'applicant') {
            $query->whereHas('application', function ($q) use ($user) {
                $q->where('student_id', $user->id);
            });
        }

        return response()->json($query->with(['application.project', 'application.student'])->paginate(15));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Interview::class);

        $validatedData = $request->validate([
            'application_id' => 'required|exists:applications,id',
            'scheduled_at'   => 'required|date',
            'meeting_link'   => 'nullable|string',
            'notes'          => 'nullable|string',
            'score'          => 'nullable|integer|min:0|max:100',
        ]);

        $interview = Interview::create($validatedData);

        return response()->json([
            'message'   => 'Interview scheduled successfully',
            'interview' => $interview,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Interview $interview)
    {
        $this->authorize('view', $interview);
        return response()->json($interview->load(['application.project', 'application.student']));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Interview $interview)
    {
        $this->authorize('update', $interview);

        $validatedData = $request->validate([
            'scheduled_at' => 'sometimes|date',
            'meeting_link' => 'nullable|string',
            'notes'        => 'sometimes|string',
            'score'        => 'sometimes|integer|min:0|max:100',
        ]);

        $interview->update($validatedData);

        return response()->json([
            'message'   => 'Interview updated successfully',
            'interview' => $interview,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Interview $interview)
    {
        $this->authorize('delete', $interview);
        $interview->delete();
        return response()->json(['message' => 'Interview deleted successfully'], 204);
    }
}
