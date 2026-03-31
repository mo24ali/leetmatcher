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
        $skillIds = [];
        $userId = auth()->id();

        foreach ($skillNames as $name) {
            $name = trim($name);
            if (empty($name)) continue;

            // Find existing skill by name (case-insensitive)
            $skill = Skill::whereRaw('LOWER(name) = ?', [strtolower($name)])->first();
            
            if (!$skill) {
                $skill = Skill::create([
                    'name' => $name,
                    'user_id' => $userId,
                    'proficiency' => 'intermediate'
                ]);
            }

            $skillIds[$skill->id] = ['level' => 'intermidiate'];
        }

        // If no skills provided, fall back to extraction
        if (empty($skillIds)) {
            $this->skillExtractor->syncToProject($project);
            return;
        }

        $project->skills()->sync($skillIds);
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
    public function applications(Project $project)
    {
        $this->authorize('update', $project);

        $applications = Application::where('project_id', $project->id)
            ->with('student')
            ->latest()
            ->get()
            ->map(fn($app) => [
                'id'       => $app->id,
                'name'     => $app->student->name,
                'email'    => $app->student->email,
                'applied'  => $app->created_at->diffForHumans(),
                'status'   => $app->status,
                'cover_letter' => $app->cover_letter,
            ]);

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
            'company' => $p->recruiter?->name ?? 'Acme Corp',
            'type' => $p->type ?? 'Remote',
            'match' => $p->match_score,
            'skills' => $p->skills->pluck('name')->toArray(),
            'description' => $p->description,
            'deadline' => $p->deadline?->format('Y-m-d'),
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
    public function getSkills(Project $project){
        $description = Project::where('id',$project->id())->pluck('description');
        
    }
}