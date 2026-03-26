<?php

declare(strict_types=1);

use App\Settings\UI\Http\Controllers\Api\V1\ChartDefinitionController;
use App\Settings\UI\Http\Controllers\Api\V1\IntegrationAccountController;
use App\Settings\UI\Http\Controllers\Api\V1\SettingEntryController;
use App\Shared\Domain\ValueObject\Uuid;
use Illuminate\Support\Facades\Route;

Route::bind('id', static function (string $value) {
    return Uuid::fromString($value);
});

Route::middleware(['user.jwt'])->prefix('v1/settings')->group(function () {
    Route::get('chart-definitions', [ChartDefinitionController::class, 'index']);
    Route::post('chart-definitions', [ChartDefinitionController::class, 'store']);
    Route::get('chart-definitions/{id}', [ChartDefinitionController::class, 'show']);
    Route::put('chart-definitions/{id}', [ChartDefinitionController::class, 'update']);
    Route::delete('chart-definitions/{id}', [ChartDefinitionController::class, 'destroy']);

    Route::get('integration-accounts', [IntegrationAccountController::class, 'index']);
    Route::post('integration-accounts', [IntegrationAccountController::class, 'store']);
    Route::get('integration-accounts/{id}', [IntegrationAccountController::class, 'show']);
    Route::put('integration-accounts/{id}', [IntegrationAccountController::class, 'update']);
    Route::patch('integration-accounts/{id}/enabled', [IntegrationAccountController::class, 'patchEnabled']);
    Route::delete('integration-accounts/{id}', [IntegrationAccountController::class, 'destroy']);

    Route::get('entries/grouped', [SettingEntryController::class, 'grouped']);
    Route::get('groups/{groupKey}', [SettingEntryController::class, 'indexByGroup'])
        ->where('groupKey', '[a-zA-Z0-9_\-\.]+');
    Route::put('entries', [SettingEntryController::class, 'upsert']);
    Route::get('entries/{id}', [SettingEntryController::class, 'show']);
    Route::delete('entries/{id}', [SettingEntryController::class, 'destroy']);
});
