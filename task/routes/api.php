<?php

use App\Http\Controllers\Api\V1\ApplicationManagerController;
use App\Http\Controllers\Api\V1\TaskController;
use Illuminate\Support\Facades\Route;

// Public endpoints (API Key required)
Route::middleware(['api.key'])->group(function () {
    Route::post('/v1/tasks', [TaskController::class, 'store']);
});

// Protected endpoints (JWT required)
Route::middleware(['auth:api'])->group(function () {
    // Task endpoints
    Route::get('/v1/tasks', [TaskController::class, 'index']);
    Route::get('/v1/tasks/{id}', [TaskController::class, 'show']);
    Route::put('/v1/tasks/{id}', [TaskController::class, 'update']);
    Route::patch('/v1/tasks/{id}/status', [TaskController::class, 'updateStatus']);

    // Application Manager endpoints
    Route::post('/v1/applications', [ApplicationManagerController::class, 'store']);
    Route::get('/v1/applications', [ApplicationManagerController::class, 'index']);
    Route::get('/v1/applications/{id}', [ApplicationManagerController::class, 'show']);
    Route::put('/v1/applications/{id}', [ApplicationManagerController::class, 'update']);
    Route::post('/v1/applications/{id}/generate-api-key', [ApplicationManagerController::class, 'generateApiKey']);
});
