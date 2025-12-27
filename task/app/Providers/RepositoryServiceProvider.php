<?php

declare(strict_types=1);

namespace App\Providers;

use Domain\ApplicationManager\Repository\ApplicationManagerRepositoryInterface;
use Domain\Task\Repository\TaskRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use Infrastructure\ApplicationManager\Repository\EloquentApplicationManagerRepository;
use Infrastructure\Task\Repository\EloquentTaskRepository;

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

