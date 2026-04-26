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
use App\Http\Controllers\AdminController;
use App\Http\Middleware\AdminOnly;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\AdminBlogPostController;
use App\Http\Controllers\BlogPostController;
Route::post('/v1/register', [AuthController::class , 'register']);
Route::post('/v1/login', [AuthController::class , 'login']);
Route::post('/v1/verify-otp', [AuthController::class, 'verifyOtp']);

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
    Route::post('/v1/profile/toggle-otp', [ProfileApiController::class, 'toggleOtp']);

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
    Route::apiResource('/v1/messages', MessageController::class);
    Route::post('/v1/reviews', [ReviewController::class, 'store']);
    Route::post('/v1/application/apply', [ApplicationController::class, 'apply']);
    

    // Interviews
    Route::post('/v1/interviews/{application}/create', [InterviewController::class, 'store']);
    Route::post('/v1/interviews/{interview}/result', [InterviewController::class, 'submitResult']);

    // Blogs
    Route::apiResource('/v1/blog-posts', BlogPostController::class);
    // Admin Routes
    Route::middleware([AdminOnly::class])->prefix('v1/admin')->group(function () {
        Route::get('/blog-posts', [AdminBlogPostController::class, 'index']);
        Route::patch('/blog-posts/{blogPost}/moderate', [AdminBlogPostController::class, 'moderate']);
        Route::delete('/blog-posts/{blogPost}', [AdminBlogPostController::class, 'destroy']);

        Route::get('/stats', [AdminController::class, 'stats']);
        Route::get('/users', [AdminController::class, 'users']);
        Route::delete('/users/{id}', [AdminController::class, 'deleteUser']);
        Route::post('/users/{id}/warn', [AdminController::class, 'warnUser']);
        Route::get('/projects', [AdminController::class, 'projects']);
        Route::delete('/projects/{id}', [AdminController::class, 'deleteUser']);
        Route::get('/activity', [AdminController::class, 'activity']);
        Route::get('/logs', [AdminController::class, 'logs']);
    });

    // Notifications
    Route::get('/v1/notifications', [NotificationController::class, 'index']);
    Route::get('/v1/notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::patch('/v1/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/v1/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
});