<?php

declare(strict_types=1);

namespace App\Settings\Infrastructure\Listener;

use App\Auth\Domain\Service\ActivityLogProducerInterface;
use App\Settings\Application\Cache\SettingsQueryCacheInterface;
use App\Settings\Domain\Event\SettingsChangedEvent;
use App\Shared\Domain\ValueObject\Uuid;
use Psr\Log\LoggerInterface;
use Throwable;

final class SettingsChangedListener
{
    public function __construct(
        private readonly SettingsQueryCacheInterface $cache,
        private readonly ActivityLogProducerInterface $activityLogProducer,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function handle(SettingsChangedEvent $event): void
    {
        $this->cache->invalidate();

        try {
            $this->activityLogProducer->log(
                userId: $event->actorId !== null ? Uuid::fromString($event->actorId) : null,
                url: $event->requestUrl ?? '/api/v1/settings',
                ipAddress: $event->ipAddress ?? '0.0.0.0',
                userAgent: $event->userAgent ?? 'settings-events-listener',
                action: sprintf('settings.%s.%s', $event->resourceType, $event->operation),
                metadata: [
                    'module' => 'settings',
                    'resource_type' => $event->resourceType,
                    'resource_id' => $event->resourceId,
                    'operation' => $event->operation,
                    'changed_fields' => $event->changedFields,
                    'before' => $event->before,
                    'after' => $event->after,
                    'actor_id' => $event->actorId,
                ]
            );
        } catch (Throwable $e) {
            $this->logger->error('Settings changed listener: activity log failed', [
                'resource_type' => $event->resourceType,
                'resource_id' => $event->resourceId,
                'operation' => $event->operation,
                'exception' => $e->getMessage(),
            ]);
        }
    }
}
