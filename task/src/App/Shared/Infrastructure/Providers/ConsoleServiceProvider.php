<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Providers;

use App\Auth\Infrastructure\Console\ActivityLogConsumerCommand;
use Illuminate\Support\ServiceProvider;

final class ConsoleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ActivityLogConsumerCommand::class,
            ]);
        }
    }
}
