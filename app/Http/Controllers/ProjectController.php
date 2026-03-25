<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Project::class);
        return response()->json(Project::with('recruiter')->paginate(10));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Project::class);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Project::class);

        $validatedData = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'deadline'    => 'required|date',
            'status'      => 'required|in:open,closed',
        ]);

        $project = Project::create([
            ...$validatedData,
            'recruiter_id' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Project created successfully',
            'project' => $project,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        $this->authorize('view', $project);
        return response()->json($project->load(['recruiter', 'applications']));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        $this->authorize('update', $project);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $validatedData = $request->validate([
            'title'       => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'deadline'    => 'sometimes|date',
            'status'      => 'sometimes|in:open,closed',
        ]);

        $project->update($validatedData);

        return response()->json([
            'message' => 'Project updated successfully',
            'project' => $project,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);
        $project->delete();
        return response()->json(['message' => 'Project deleted successfully'], 204);
    }

    /**
     * Get matching profiles for a project.
     */
    public function matches(Project $project, \App\Services\MatchingService $matchingService)
    {
        $this->authorize('view', $project);

        $profiles = \App\Models\Profile::with(['user', 'skills'])->get();

        $matches = $profiles->map(function ($profile) use ($project, $matchingService) {
            $profile->match_score = $matchingService->calculateMatch($project, $profile);
            return $profile;
        })
        ->filter(fn($profile) => $profile->match_score > 0)
        ->sortByDesc('match_score')
        ->values();

        return response()->json($matches);
    }

    /**
     * Get recommended projects for the authenticated user.
     */
    public function recommended(Request $request, \App\Services\MatchingService $matchingService)
    {
        $profile = $request->user()->profile;

        if (!$profile) {
            return response()->json([]);
        }

        $recommendations = $matchingService->getRecommendedProjects($profile);

        // Map to expected frontend format
        $formatted = $recommendations->map(fn($p) => [
            'id' => $p->id,
            'title' => $p->title,
            'company' => $p->recruiter?->name ?? 'Acme Corp',
            'type' => $p->type ?? 'Remote',
            'match' => $p->match_score,
            'skills' => $p->skills->pluck('name')->toArray(),
            'description' => $p->description,
            'deadline' => $p->deadline?->format('Y-m-d'),
        ]);

        return response()->json($formatted);
    }
}
