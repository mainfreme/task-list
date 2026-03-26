<?php

declare(strict_types=1);

namespace App\Task\UI\Http\routes;

use App\Task\UI\Http\Controllers\Api\V1\TaskController;
use App\Task\UI\Http\Controllers\Api\V1\TaskStatsController;
use App\Task\UI\Http\Controllers\Api\V1\TaskTimeController;
use Illuminate\Support\Facades\Route;

$taskId = '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}';

// Task endpoints - protected with user JWT (login token)
Route::middleware(['user.jwt'])->prefix('v1')->group(function () use ($taskId) {
    Route::get('tasks/stats/status', [TaskStatsController::class, 'statusesCount']);
    Route::get('tasks/{id}/time', [TaskTimeController::class, 'show'])->where('id', $taskId);
    Route::post('tasks/{id}/time', [TaskTimeController::class, 'store'])->where('id', $taskId);
    Route::patch('tasks/{id}/status', [TaskController::class, 'updateStatus'])
        ->where('id', $taskId);
    Route::apiResource('tasks', TaskController::class);
});
