<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Application::class);

        $user = auth()->user();
        $query = Application::query();

        if ($user->role === 'recruiter') {
            $query->whereHas('project', function ($q) use ($user) {
                $q->where('recruiter_id', $user->id);
            });
        } elseif ($user->role === 'student' || $user->role === 'applicant') {
            $query->where('student_id', $user->id);
        }

        return response()->json($query->with(['project', 'student'])->paginate(15));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Application::class);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Application::class);

        $validatedData = $request->validate([
            'project_id'   => 'required|exists:projects,id',
            'cover_letter' => 'required|string',
        ]);

        $application = Application::create([
            ...$validatedData,
            'student_id' => $request->user()->id,
            'status'     => 'pending',
        ]);

        return response()->json([
            'message'     => 'Application submitted successfully',
            'application' => $application,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Application $application)
    {
        $this->authorize('view', $application);
        return response()->json($application->load(['project', 'student']));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Application $application)
    {
        $this->authorize('update', $application);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Application $application)
    {
        $this->authorize('update', $application);

        $validatedData = $request->validate([
            'status'       => 'sometimes|in:pending,accepted,rejected',
            'cover_letter' => 'sometimes|string',
        ]);

        $application->update($validatedData);

        return response()->json([
            'message'     => 'Application updated successfully',
            'application' => $application,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Application $application)
    {
        $this->authorize('delete', $application);
        $application->delete();
        return response()->json(['message' => 'Application withdrawn successfully'], 204);
    }
}
