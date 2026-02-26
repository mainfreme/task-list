<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Providers;

use App\ApplicationManager\Domain\Repository\ApplicationManagerRepositoryInterface;
use App\ApplicationManager\Infrastructure\Repository\EloquentApplicationManagerRepository;
use App\Auth\Domain\Repository\UserRepositoryInterface;
use App\Auth\Domain\Service\JwtTokenServiceInterface;
use App\Auth\Infrastructure\Repository\EloquentUserRepository;
use App\Auth\Infrastructure\Service\JwtTokenService;
use App\Profile\Domain\Repository\UserProfileRepository;
use App\Profile\Infrastructure\Repository\EloquentUserProfileRepository;
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

        $this->app->bind(
            JwtTokenServiceInterface::class,
            JwtTokenService::class
        );

        $this->app->bind(
            UserProfileRepository::class,
            EloquentUserProfileRepository::class
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
