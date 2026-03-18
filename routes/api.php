<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CvController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ─── Public routes ────────────────────────────────────────────────────────────
Route::post('/v1/register', [AuthController::class, 'register']);
Route::post('/v1/login',    [AuthController::class, 'login']);

// ─── Authenticated routes (Sanctum token required) ───────────────────────────
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user',        fn(Request $r) => $r->user());
    Route::get('/v1/me',       [AuthController::class, 'me']);
    Route::post('/v1/logout',  [AuthController::class, 'logout']);
    Route::post('/v1/cv/upload', [CvController::class, 'upload']);
});