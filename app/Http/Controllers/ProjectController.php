<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Application;
use Illuminate\Http\Request;
use App\Models\Skill;
use App\Models\Profile;
use App\Services\MatchingService;
use App\Services\SkillExtractionService;
class ProjectController extends Controller
{
    protected $skillExtractor;

    public function __construct(SkillExtractionService $skillExtractor)
    {
        $this->skillExtractor = $skillExtractor;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Public job board view
        return response()->json(Project::with(['recruiter', 'skills'])->where('status', 'open')->latest()->paginate(10));
    }

    /**
     * Recruiter's own listings.
     */
    public function myProjects(Request $request)
    {
        $user = $request->user();
        return response()->json(
            Project::where('recruiter_id', $user->id)
                ->with(['skills'])
                ->withCount('applications')
                ->latest()
                ->get()
        );
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

        $this->syncManualSkills($project, $request->input('skills', []));

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

        if ($request->has('skills')) {
            $this->syncManualSkills($project, $request->input('skills', []));
        } elseif ($request->has('description') || $request->has('title')) {
            $this->skillExtractor->syncToProject($project);
        }

        return response()->json([
            'message' => 'Project updated successfully',
            'project' => $project->load('skills'),
        ]);
    }

    /**
     * Helper to sync manual skill names to a project.
     */
    protected function syncManualSkills(Project $project, array $skillNames)
    {
        // Use service to handle Three-Step Pattern:
        // 1. Ensure skills exist in canonical 'skills' table
        // 2. Sync to 'project_skills' table
        $this->skillExtractor->syncToProject($project, $skillNames);
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
    public function matches(Project $project, MatchingService $matchingService)
    {
        $this->authorize('view', $project);

        $profiles = Profile::with(['user', 'skills'])->get();

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
     * Get applications for a project.
     */
    public function applications(Project $project, MatchingService $matchingService)
    {
        $this->authorize('update', $project);

        $applications = Application::where('project_id', $project->id)
            ->with(['student.profile.skills', 'student.skills'])
            ->latest()
            ->get()
            ->map(function ($app) use ($project, $matchingService) {
                $profile = $app->student->profile;
                $skills = $profile ? $profile->skills->pluck('name') : $app->student->skills->pluck('name');
                $matchScore = $profile ? $matchingService->calculateMatch($project, $profile) : 0;
                
                return [
                    'id'           => $app->id,
                    'name'         => $app->student->name,
                    'email'        => $app->student->email,
                    'applied'      => $app->created_at->diffForHumans(),
                    'status'       => $app->status,
                    'cover_letter' => $app->cover_letter,
                    'description'  => $app->cover_letter ?: ($profile->bio ?? 'No description provided'),
                    'picture'      => $profile->avatar_url ?? null,
                    'phone'        => $profile->phone ?? 'N/A',
                    'skills'       => $skills,
                    'matchPercent' => $matchScore . '%',
                ];
            });

        return response()->json($applications);
    }

    /**
     * Get recommended projects for the authenticated user.
     */
    public function recommended(Request $request, MatchingService $matchingService)
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
            'description' => $p->description,
            'skills' => $p->skills->pluck('name')->toArray(),
            'recruiter' => [
                'name' => $p->recruiter?->name,
                'company' => $p->recruiter?->company_name ?? 'Acme Corp', // Fallback or use company_name field if it exists
                'details' => $p->recruiter?->email, // Using email as details placeholder
                'avatar' => $p->recruiter?->avatar_url ?? "https://i.pravatar.cc/150?u={$p->recruiter_id}",
                'reviews' => $p->recruiter?->reviewsReceived->map(fn($r) => [
                    'rating' => $r->rating,
                    'comment' => $r->comment,
                    'reviewer_name' => $r->reviewer?->name,
                ])->toArray() ?? []
            ]
        ]);

        return response()->json($formatted);
    }

    public function recruiterStats(Request $request)
    {
        $user = $request->user();
        
        $projectsIds = Project::where('recruiter_id', $user->id)->pluck('id');
        
        $totalProjects     = Project::where('recruiter_id', $user->id)->count();
        $totalApplications   = Application::whereIn('project_id', $projectsIds)->count();
        $pendingReviews      = Application::whereIn('project_id', $projectsIds)->where('status', 'pending')->count();
        $scheduledInterviews = \App\Models\Interview::whereHas('application', function($q) use ($projectsIds) {
            $q->whereIn('project_id', $projectsIds);
        })->count();

        // Skill distribution among recruiter's projects
        $skillsDistribution = Skill::whereHas('projects', function($q) use ($user) {
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
                'total_projects'      => $totalProjects,
                'total_applications'  => $totalApplications,
                'pending_reviews'     => $pendingReviews,
                'scheduled_interviews' => $scheduledInterviews,
            ],
            'skills_distribution' => $skillsDistribution,
        ]);
    }
    public function recruiterMatches(Request $request, MatchingService $matchingService)
    {
        $matches = $matchingService->getRecruiterMatches($request->user());

        // Map to format suitable for TopMatches.vue
        $formatted = $matches->map(fn($p) => [
            'id' => $p->id,
            'name' => $p->user?->name,
            'avatar' => $p->user?->avatar_url ?? "https://i.pravatar.cc/150?u={$p->id}",
            'skills' => $p->skills->pluck('name')->toArray(),
            'experience' => '3+ years experience', // Placeholder for experience
            'qualifications' => $p->education_level ?? 'B.S. in Computer Science',
            'reviews' => $p->user?->reviewsReceived->map(fn($r) => [
                'rating' => $r->rating,
                'comment' => $r->comment,
                'reviewer_name' => $r->reviewer?->name,
            ])->toArray() ?? [],
            'match_score' => $p->match_score,
        ]);

        return response()->json($formatted);
    }
    public function getSkills(Project $project){
        $description = Project::where('id',$project->id())->pluck('description');
        
    }
}