<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Providers;

use App\ApplicationManager\Domain\Repository\ApplicationManagerRepositoryInterface;
use App\Task\Domain\Repository\TaskRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use App\ApplicationManager\Infrastructure\Repository\EloquentApplicationManagerRepository;
use App\Task\Infrastructure\Repository\EloquentTaskRepository;

final class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            ApplicationManagerRepositoryInterface::class,
            EloquentApplicationManagerRepository::class
        );

        $this->app->bind(
            TaskRepositoryInterface::class,
            EloquentTaskRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
