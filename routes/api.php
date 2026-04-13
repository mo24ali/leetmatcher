<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CvController;
use App\Http\Controllers\Api\ProfileApiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\InterviewController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/v1/register', [AuthController::class , 'register']);
Route::post('/v1/login', [AuthController::class , 'login']);

Route::middleware('auth')->group(function () {
    Route::get('/user', fn(Request $r) => $r->user());
    Route::get('/v1/me', [AuthController::class , 'me']);
    Route::post('/v1/logout', [AuthController::class , 'logout']);

    // CV upload & legacy skills endpoint
    Route::post('/v1/cv/upload', [CvController::class , 'upload']);
    Route::get('/v1/profile/skills', [ProfileController::class , 'getSkills']);

    // Profile management
    Route::get('/v1/profile', [ProfileApiController::class , 'show']);
    Route::patch('/v1/profile', [ProfileApiController::class , 'update']);
    Route::post('/v1/profile/password', [ProfileApiController::class , 'changePassword']);
    Route::post('/v1/profile/avatar', [ProfileApiController::class , 'uploadAvatar']);
    Route::post('/v1/profile/skills', [ProfileApiController::class , 'addSkill']);
    Route::delete('/v1/profile/skills/{skill}', [ProfileApiController::class , 'removeSkill']);

    // Dashboard & Stats
    Route::get('/v1/recruiter/stats', [ProjectController::class , 'recruiterStats']);
    Route::get('/v1/recruiter/listings', [ProjectController::class, 'myProjects']);
    Route::get('/v1/recruiter/matches', [ProjectController::class, 'recruiterMatches']);
    Route::get('/v1/skills/search', [SkillController::class, 'search']);
    Route::get('/v1/applicant/stats', [ApplicationController::class , 'applicantStats']);

    // Job listings and applications
    Route::get('/v1/projects/recommended', [ProjectController::class , 'recommended']);
    Route::get('/v1/projects/{project}/matches', [ProjectController::class , 'matches']);
    Route::get('/v1/projects/{project}/applications', [ProjectController::class , 'applications']);
    Route::apiResource('/v1/projects', ProjectController::class);
    Route::apiResource('/v1/applications', ApplicationController::class);
    Route::apiResource('/v1/interviews', InterviewController::class);
    // Real-time Messaging
    Route::get('/v1/conversations', [MessageController::class, 'index']);
    Route::post('/v1/conversations/start', [MessageController::class, 'start']);
    Route::get('/v1/conversations/{conversation}', [MessageController::class, 'show']);
    Route::post('/v1/conversations/{conversation}/messages', [MessageController::class, 'store']);
    Route::post('/v1/application/apply', [ApplicationController::class, 'apply']);
    

    // Interviews
    Route::post('/v1/interviews/{application}/create', [InterviewController::class, 'store']);
});