<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Interview;


Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('interview.{interviewId}', function ($user, $interviewId) {
    $interview = Interview::find($interviewId);
    if (!$interview) return false;

    $application = $interview->application;
    
    // Check if user is the student or the recruiter
    $isStudent = (int) $user->id === (int) $application->student_id;
    $isRecruiter = (int) $user->id === (int) $application->project->recruiter_id;

    if ($isStudent || $isRecruiter) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'role' => $isRecruiter ? 'recruiter' : 'student'
        ];
    }

    return false;
});
