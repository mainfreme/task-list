<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Providers;

use App\Domain\ApplicationManager\Repository\ApplicationManagerRepositoryInterface;
use App\Domain\Task\Repository\TaskRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use App\Infrastructure\ApplicationManager\Repository\EloquentApplicationManagerRepository;
use App\Infrastructure\Task\Repository\EloquentTaskRepository;

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

