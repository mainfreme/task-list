<?php

// Public endpoints (API Key required)
// Route::middleware(['api.key'])->group(function () {
//     Route::post('/v1/tasks', [TaskController::class, 'store']);
// });

// Protected endpoints (JWT required) 
// Route::middleware(['auth:api'])->group(function () {
    // Task endpoints
    // Route::get('/v1/tasks', [TaskController::class, 'index']);
    // Route::get('/v1/tasks/{id}', [TaskController::class, 'show']);
    // Route::put('/v1/tasks/{id}', [TaskController::class, 'update']);
    // Route::post('/v1/tasks', [TaskController::class, 'store']);
    // Route::delete('/v1/tasks/{id}', [TaskController::class, 'destroy']);
    // Route::patch('/v1/tasks/{id}/status', [TaskController::class, 'updateStatus']);


// });

require_once __DIR__ . '/RouteLoader.php';

\Routes\RouteLoader::loadApiRoutes();