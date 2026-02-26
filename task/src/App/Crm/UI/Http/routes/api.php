<?php

declare(strict_types=1);

namespace App\Crm\UI\Http\routes;

use App\Crm\UI\Http\Controllers\Api\V1\CrmController;
use App\Shared\Domain\ValueObject\Uuid;
use Illuminate\Support\Facades\Route;

// Custom route binding for Uuid - converts string to Uuid Value Object
Route::bind('id', function (string $value) {
    return Uuid::fromString($value);
});

// CRM endpoints - protected with JWT authentication
Route::middleware(['user.jwt'])->group(function () {
    Route::post('/v1/crm', [CrmController::class, 'store']);
    Route::get('/v1/crm', [CrmController::class, 'index']);
    Route::get('/v1/crm/{id}', [CrmController::class, 'show']);
    Route::put('/v1/crm/{id}', [CrmController::class, 'update']);
    Route::delete('/v1/crm/{id}', [CrmController::class, 'destroy']);
});
