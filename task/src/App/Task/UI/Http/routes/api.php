<?php

declare(strict_types=1);

namespace App\Task\UI\Http\routes;

use App\Task\UI\Http\Controllers\Api\V1\TaskController;
use App\Task\UI\Http\Controllers\Api\V1\TaskStatsController;
use Illuminate\Support\Facades\Route;

// Task endpoints - protected with user JWT (login token)
Route::middleware(['user.jwt'])->prefix('v1')->group(function () {
    Route::get('tasks/stats/status', [TaskStatsController::class, 'statusesCount']);
    Route::patch('tasks/{id}/status', [TaskController::class, 'updateStatus'])
        ->where('id', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');
    Route::apiResource('tasks', TaskController::class);
});
