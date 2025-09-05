<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\JobStageController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\ApplicationAttachmentController;
use App\Http\Controllers\ApplicationActivityController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\JobTagController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// --------------------
// Public routes
// --------------------

// Login for recruiter/admin
Route::post('/login', [AuthController::class, 'authenticate']);

// Candidate can register (optional)
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->get('/me', [AuthController::class, 'me']);
// --------------------
// Protected routes (Sanctum + role-based)
// --------------------
Route::middleware(['auth:sanctum'])->group(function () {

    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);
    // Candidate routes
    Route::get('my-applications', [ApplicationController::class, 'myApplications']);

    // Stage history
    Route::get('applications/{id}/stages', [ApplicationController::class, 'stages']);

    // --------------------
    // Admin-only routes
    // --------------------
    Route::middleware(\App\Http\Middleware\RoleMiddleware::class . ':admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('job-stages', JobStageController::class);
        Route::resource('tags', TagController::class);
        Route::resource('job-tags', JobTagController::class);
    });

    // --------------------
    // Admin + Recruiter routes
    // --------------------
    Route::middleware(\App\Http\Middleware\RoleMiddleware::class . ':admin,recruiter,candidate')->group(function () {
        Route::resource('jobs', JobController::class);
        Route::resource('candidates', CandidateController::class);
        
        Route::get('my-profile', [CandidateController::class, 'myProfile']);
        Route::post('update-my-profile', [CandidateController::class, 'updateMyProfile']);

        Route::resource('applications', ApplicationController::class);
        Route::post('applications/{id}/transition', [ApplicationController::class, 'transition']);
        Route::get('applications/status-counts', [ApplicationController::class, 'statusCounts']);

        Route::resource('application-attachments', ApplicationAttachmentController::class);
        Route::resource('application-activities', ApplicationActivityController::class);
    });

    // --------------------
    // Candidate-specific routes (example)
    // --------------------
    Route::middleware(\App\Http\Middleware\RoleMiddleware::class . ':candidate')->group(function () {
        // Candidate can view only their own applications
        Route::get('my-applications', [ApplicationController::class, 'myApplications']);
    });
});
