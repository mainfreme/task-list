<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Providers;

use App\ApplicationManager\Domain\Repository\ApplicationManagerRepositoryInterface;
use App\ApplicationManager\Infrastructure\Repository\EloquentApplicationManagerRepository;
use App\Auth\Domain\Repository\UserRepositoryInterface;
use App\Auth\Infrastructure\Repository\EloquentUserRepository;
use App\Task\Domain\Repository\TaskRepositoryInterface;
use App\Task\Infrastructure\Repository\EloquentTaskRepository;
use Illuminate\Support\ServiceProvider;

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

        $this->app->bind(
            UserRepositoryInterface::class,
            EloquentUserRepository::class
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
