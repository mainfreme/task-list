<?php

declare(strict_types=1);

namespace App\ApplicationManager\UI\Http\routes;

use App\ApplicationManager\UI\Http\Controllers\Api\V1\ApplicationManagerController;
use Illuminate\Support\Facades\Route;

// Application Manager endpoints
Route::middleware(['jwt'])->group(function () {
    Route::post('/v1/applications', [ApplicationManagerController::class, 'store']);
    Route::get('/v1/applications', [ApplicationManagerController::class, 'index']);
    Route::get('/v1/applications/{id}', [ApplicationManagerController::class, 'show']);
    Route::put('/v1/applications/{id}', [ApplicationManagerController::class, 'update']);


});

Route::post('/v1/applications/{id}/generate-api-key', [ApplicationManagerController::class, 'generateApiKey']);
Route::post('/v1/applications/{id}/generate-jwt-token', [ApplicationManagerController::class, 'generateJwtToken']);
