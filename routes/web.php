<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CvController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\InterviewController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->group(function () {
    Route::post('/v1/register',   [AuthController::class, 'register']);
    Route::post('/v1/login',      [AuthController::class, 'login']);
    // Route::post('/v1/otp/verify', [AuthController::class, 'verifyOtp']); // Future OTP verification logic

    Route::middleware('auth')->group(function () {
        Route::get('/user',        fn(Request $r) => $r->user());
        Route::get('/v1/me',       [AuthController::class, 'me']);
        Route::post('/v1/logout',  [AuthController::class, 'logout']);
        Route::post('/v1/cv/upload', [CvController::class, 'upload']);
        Route::get('/v1/profile/skills', [ProfileController::class, 'getSkills']);

        Route::get('/v1/projects/{project}/matches', [ProjectController::class, 'matches']);
        Route::apiResource('/v1/projects', ProjectController::class);
        Route::apiResource('/v1/applications', ApplicationController::class);
        Route::apiResource('/v1/interviews', InterviewController::class);
        Route::apiResource('/v1/messages', MessageController::class);
    });
});

Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');
