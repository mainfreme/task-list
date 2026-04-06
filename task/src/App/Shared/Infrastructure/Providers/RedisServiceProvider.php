<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Providers;

use App\Shared\Domain\Redis\RedisServiceInterface;
use App\Shared\Infrastructure\Redis\LaravelRedisService;
use Illuminate\Support\ServiceProvider;

final class RedisServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(RedisServiceInterface::class, function () {
            $connection = config('redis_services.connection');

            return new LaravelRedisService(
                is_string($connection) && $connection !== '' ? $connection : null
            );
        });
    }
}
