<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Articles\UI\Http\Controllers\ArticleController;

Route::prefix('api')->group(function () {
    Route::apiResource('articles', ArticleController::class);
});