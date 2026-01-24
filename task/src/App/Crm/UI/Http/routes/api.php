<?php

declare(strict_types=1);

namespace App\Crm\UI\Http\routes;

use Illuminate\Support\Facades\Route;
use App\Crm\Domain\ValueObject\Uuid\ClientId;
use App\Crm\UI\Http\Controllers\Api\V1\CrmController;

// Custom route binding for ClientId - converts string to ClientId Value Object
Route::bind('id', function (string $value) {
    return ClientId::fromString($value);
});

// CRM endpoints - protected with JWT authentication
Route::middleware(['jwt'])->group(function () {
    Route::post('/v1/crm', [CrmController::class, 'store']);
    Route::get('/v1/crm', [CrmController::class, 'index']);
    Route::get('/v1/crm/{id}', [CrmController::class, 'show']);
    Route::put('/v1/crm/{id}', [CrmController::class, 'update']);
    Route::delete('/v1/crm/{id}', [CrmController::class, 'destroy']);
});

