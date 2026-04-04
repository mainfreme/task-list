<?php

declare(strict_types=1);

use App\Ops\UI\Http\Controllers\Api\V1\DeployWebhookController;
use Illuminate\Support\Facades\Route;

Route::middleware(['deploy.webhook'])->prefix('v1/deploy')->group(function () {
    Route::post('/error', [DeployWebhookController::class, 'reportError']);
});
