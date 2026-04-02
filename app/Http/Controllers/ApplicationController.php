<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
class ApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Application::class);

        $user = Auth::user();
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
    
    // public function index2(){
    //     $this->authorize('viewAny', Application::class);
        
    //     $user = Auth::user();

    //     $query = Application::query(); // preparing a query for the Application eloquent model

    //     if($user->role === 'recruiter'){
    //         $query->whereHas('project', function ($q) use ($user){
    //             $q->where('recruiter_id', $user->id);
    //         });
    //     } elseif ($user->role === "student" || $user->role === 'applicant'){
    //         $query->where('student_id', $user->id);
    //     }
    //     return response()->json($query->with(['project', 'student'])->paginate(10));
    // }

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

    /**
     * Get statistics for the applicant dashboard.
     */
    public function applicantStats(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'applied'  => Application::where('student_id', $user->id)->count(),
            'pending'  => Application::where('student_id', $user->id)->where('status', 'pending')->count(),
            'accepted' => Application::where('student_id', $user->id)->where('status', 'accepted')->count(),
            'rejected' => Application::where('student_id', $user->id)->where('status', 'rejected')->count(),
        ]);
    }

    public function apply(Request $request)
{
    $user = $request->user();

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthenticated'
        ], 401);
    }

    $request->validate([
        'project_id' => 'required|exists:projects,id'
    ]);

    $projectId = $request->project_id;

    $existingApplication = Application::where('student_id', $user->id)
        ->where('project_id', $projectId)
        ->first();

    if ($existingApplication) {
        return response()->json([
            'success' => false,
            'message' => 'You already applied to the project'
        ]);
    }

    Application::create([
        'student_id' => $user->id,
        'project_id' => $projectId,
        'status' => 'pending',
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Applied successfully'
    ]);
}
}
