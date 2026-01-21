<?php

declare(strict_types=1);

namespace App\Task\UI\Http\routes;

use Illuminate\Support\Facades\Route;
use App\Task\UI\Http\Controllers\Api\V1\TaskController;

   // Task endpoints - protected with JWT authentication
   Route::middleware(['jwt'])->group(function () {
       Route::get('/v1/tasks', [TaskController::class, 'index']);
       Route::get('/v1/tasks/{id}', [TaskController::class, 'show']);
       Route::put('/v1/tasks/{id}', [TaskController::class, 'update']);
       Route::post('/v1/tasks', [TaskController::class, 'store']);
       Route::delete('/v1/tasks/{id}', [TaskController::class, 'destroy']);
       Route::patch('/v1/tasks/{id}/status', [TaskController::class, 'updateStatus']);
   });