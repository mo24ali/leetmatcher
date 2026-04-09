<?php

namespace App\Policies;

use App\Models\Interview;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class InterviewPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Interview $interview): bool
    {
        return $user->role === 'admin' || 
               ($user->role === 'recruiter' && $user->id === $interview->application->project->recruiter_id) || 
               ($user->role === 'student' && $user->id === $interview->application->student_id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === 'recruiter' || $user->role === 'admin';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Interview $interview): bool
    {
        return $user->role === 'admin' || 
               ($user->role === 'recruiter' && $user->id === $interview->application->project->recruiter_id) ||
               (in_array($user->role, ['student', 'applicant']) && $user->id === $interview->application->student_id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Interview $interview): bool
    {
        return $user->role === 'admin' || 
               ($user->role === 'recruiter' && $user->id === $interview->application->project->recruiter_id);
    }
}
