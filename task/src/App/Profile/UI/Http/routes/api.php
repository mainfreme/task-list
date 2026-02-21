<?php

declare(strict_types=1);

namespace App\Profile\UI\Http\routes;

use App\Profile\UI\Http\Controllers\Api\V1\ProfileController;
use Illuminate\Support\Facades\Route;

// Application Manager endpoints
Route::middleware(['jwt'])->group(function () {
    Route::get('/v1/me', [ProfileController::class, 'show']);

    Route::put('/v1/me', [ProfileController::class, 'update']);

});
