<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ApplicationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin' || $user->role === 'recruiter' || $user->role === 'student';
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Application $application): bool
    {
        return $user->role === 'admin' || 
               ($user->role === 'student' && $user->id === $application->student_id) || 
               ($user->role === 'recruiter' && $user->id === $application->project->recruiter_id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === 'student';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Application $application): bool
    {
        // Recruiters update status, Students might update cover letter
        if ($user->role === 'admin') return true;
        if ($user->role === 'recruiter') {
            return $user->id === $application->project->recruiter_id;
        }
        if ($user->role === 'student') {
            return $user->id === $application->student_id;
        }
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Application $application): bool
    {
        return $user->role === 'admin' || 
               ($user->role === 'student' && $user->id === $application->student_id);
    }
}
