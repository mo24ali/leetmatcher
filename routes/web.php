<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CvController;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->group(function () {
    // ─── Public routes ────────────────────────────────────────────────────────────
    Route::post('/v1/register', [AuthController::class, 'register']);
    Route::post('/v1/login',    [AuthController::class, 'login']);

    // ─── Authenticated routes ────────────────────────────────────────────────────
    Route::middleware('auth')->group(function () {
        Route::get('/user',        fn(Request $r) => $r->user());
        Route::get('/v1/me',       [AuthController::class, 'me']);
        Route::post('/v1/logout',  [AuthController::class, 'logout']);
        Route::post('/v1/cv/upload', [CvController::class, 'upload']);
        Route::get('/v1/profile/skills', [ProfileController::class, 'getSkills']);
    });
});

Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');
