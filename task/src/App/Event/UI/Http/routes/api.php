<?php

declare(strict_types=1);

namespace App\Event\UI\Http\routes;

use App\Event\UI\Http\Controllers\Api\V1\EventController;
use Illuminate\Support\Facades\Route;

Route::middleware(['user.jwt'])->prefix('v1')->group(function () {
    Route::get('events/filters', [EventController::class, 'filters']);
    Route::get('events', [EventController::class, 'index']);
});
