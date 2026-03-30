<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Application;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    protected $skillExtractor;

    public function __construct(\App\Services\SkillExtractionService $skillExtractor)
    {
        $this->skillExtractor = $skillExtractor;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Project::class);
        return response()->json(Project::with('recruiter')->paginate(10));
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

        // Auto-extract skills
        $this->skillExtractor->syncToProject($project);

        return response()->json([
            'message' => 'Project created successfully',
            'project' => $project->load('skills'),
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

    /**
     * Get statistics for the recruiter dashboard.
     */
    public function recruiterStats(Request $request)
    {
        $user = $request->user();
        
        $projectsIds = Project::where('recruiter_id', $user->id)->pluck('id');
        
        $totalProjects = $projectsIds->count();
        $totalApplications = Application::whereIn('project_id', $projectsIds)->count();
        $pendingReviews = Application::whereIn('project_id', $projectsIds)->where('status', 'pending')->count();
        
        // Skill distribution among recruiter's projects
        $skillsDistribution = \App\Models\Skill::whereHas('projects', function($q) use ($user) {
            $q->where('recruiter_id', $user->id);
        })
        ->withCount(['projects' => function($q) use ($user) {
            $q->where('recruiter_id', $user->id);
        }])
        ->orderByDesc('projects_count')
        ->limit(10)
        ->get()
        ->map(fn($s) => ['name' => $s->name, 'count' => $s->projects_count]);

        return response()->json([
            'stats' => [
                'total_projects'     => $totalProjects,
                'total_applications' => $totalApplications,
                'pending_reviews'    => $pendingReviews,
            ],
            'skills_distribution' => $skillsDistribution,
            'recent_projects' => Project::where('recruiter_id', $user->id)
                                        ->withCount('applications')
                                        ->latest()
                                        ->limit(5)
                                        ->get(),
        ]);
    }
}
