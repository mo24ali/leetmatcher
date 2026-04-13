<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.{conversationId}', function ($user, $conversationId) {
    $conversation = \App\Models\Conversation::find($conversationId);
    if (!$conversation) return false;
    return $conversation->user_one_id === $user->id || $conversation->user_two_id === $user->id;
});

Broadcast::channel('interview.{interviewId}', function ($user, $interviewId) {
    $interview = \App\Models\Interview::find($interviewId);
    if (!$interview) return false;
    
    $studentId = $interview->application->student_id;
    $recruiterId = $interview->application->project->recruiter_id;
    
    return $user->id === $studentId || $user->id === $recruiterId;
});
