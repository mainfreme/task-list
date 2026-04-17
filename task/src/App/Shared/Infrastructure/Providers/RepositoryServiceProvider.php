<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Providers;

use App\ApplicationManager\Domain\Event\ApplicationManagerPersistedEvent;
use App\ApplicationManager\Domain\Repository\ApplicationManagerRepositoryInterface;
use App\ApplicationManager\Domain\Service\ApplicationJwtTokenGeneratorInterface;
use App\ApplicationManager\Infrastructure\Cache\ApplicationManagerCacheStore;
use App\ApplicationManager\Infrastructure\Listener\ApplicationManagerPersistedListener;
use App\ApplicationManager\Infrastructure\Repository\CachingApplicationManagerRepository;
use App\ApplicationManager\Infrastructure\Repository\EloquentApplicationManagerRepository;
use App\ApplicationManager\Infrastructure\Service\FirebaseApplicationJwtTokenGenerator;
use App\Auth\Domain\Repository\ActivityLogRepositoryInterface;
use App\Auth\Domain\Repository\UserRepositoryInterface;
use App\Auth\Domain\Service\ActivityLogProducerInterface;
use App\Auth\Domain\Service\JwtTokenServiceInterface;
use App\Auth\Infrastructure\Repository\EloquentActivityLogRepository;
use App\Auth\Infrastructure\Repository\EloquentUserRepository;
use App\Auth\Infrastructure\Service\ActivityLogProducer;
use App\Auth\Infrastructure\Service\JwtTokenService;
use App\Crm\Application\Cache\ListClientsQueryCacheInterface;
use App\Crm\Domain\Repository\AddressRepositoryInterface;
use App\Crm\Domain\Repository\ClientRepositoryInterface;
use App\Crm\Infrastructure\Cache\RedisListClientsQueryCache;
use App\Crm\Infrastructure\Repository\EloquentAddressRepository;
use App\Crm\Infrastructure\Repository\EloquentClientRepository;
use App\Ops\Domain\Repository\DeployFailureRepositoryInterface;
use App\Ops\Infrastructure\Repository\EloquentDeployFailureRepository;
use App\Profile\Domain\Repository\UserProfileRepository;
use App\Profile\Infrastructure\Repository\EloquentUserProfileRepository;
use App\Settings\Domain\Repository\ChartDefinitionRepositoryInterface;
use App\Settings\Domain\Repository\IntegrationAccountRepositoryInterface;
use App\Settings\Domain\Repository\SettingEntryRepositoryInterface;
use App\Settings\Infrastructure\Repository\EloquentChartDefinitionRepository;
use App\Settings\Infrastructure\Repository\EloquentIntegrationAccountRepository;
use App\Settings\Infrastructure\Repository\EloquentSettingEntryRepository;
use App\Shared\Infrastructure\MessageBroker\MessageProducerInterface;
use App\Shared\Infrastructure\MessageBroker\RabbitMQConnection;
use App\Shared\Infrastructure\MessageBroker\RabbitMQProducer;
use App\Task\Domain\Repository\TaskRepositoryInterface;
use App\Task\Domain\Repository\TaskTimeSessionRepositoryInterface;
use App\Task\Infrastructure\Repository\EloquentTaskRepository;
use App\Task\Infrastructure\Repository\EloquentTaskTimeSessionRepository;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

final class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(EloquentApplicationManagerRepository::class);
        $this->app->singleton(ApplicationManagerCacheStore::class);
        $this->app->bind(
            ApplicationManagerRepositoryInterface::class,
            CachingApplicationManagerRepository::class
        );

        $this->app->bind(
            ApplicationJwtTokenGeneratorInterface::class,
            FirebaseApplicationJwtTokenGenerator::class
        );

        $this->app->bind(
            TaskRepositoryInterface::class,
            EloquentTaskRepository::class
        );

        $this->app->bind(
            TaskTimeSessionRepositoryInterface::class,
            EloquentTaskTimeSessionRepository::class
        );

        $this->app->bind(
            ClientRepositoryInterface::class,
            EloquentClientRepository::class
        );

        $this->app->singleton(ListClientsQueryCacheInterface::class, RedisListClientsQueryCache::class);

        $this->app->bind(
            AddressRepositoryInterface::class,
            EloquentAddressRepository::class
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

        $this->app->bind(
            ActivityLogRepositoryInterface::class,
            EloquentActivityLogRepository::class
        );

        $this->app->singleton(RabbitMQConnection::class, function ($app) {
            return new RabbitMQConnection(
                host: config('rabbitmq.host'),
                port: config('rabbitmq.port'),
                user: config('rabbitmq.user'),
                password: config('rabbitmq.password'),
                vhost: config('rabbitmq.vhost')
            );
        });

        $this->app->bind(
            MessageProducerInterface::class,
            RabbitMQProducer::class
        );

        $this->app->bind(
            ActivityLogProducerInterface::class,
            ActivityLogProducer::class
        );

        $this->app->bind(
            ChartDefinitionRepositoryInterface::class,
            EloquentChartDefinitionRepository::class
        );

        $this->app->bind(
            IntegrationAccountRepositoryInterface::class,
            EloquentIntegrationAccountRepository::class
        );

        $this->app->bind(
            SettingEntryRepositoryInterface::class,
            EloquentSettingEntryRepository::class
        );

        $this->app->bind(
            DeployFailureRepositoryInterface::class,
            EloquentDeployFailureRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Event::listen(
            ApplicationManagerPersistedEvent::class,
            ApplicationManagerPersistedListener::class
        );
    }
}
