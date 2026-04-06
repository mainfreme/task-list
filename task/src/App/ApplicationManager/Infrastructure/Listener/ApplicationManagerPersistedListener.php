<?php

declare(strict_types=1);

namespace App\ApplicationManager\Infrastructure\Listener;

use App\ApplicationManager\Domain\Event\ApplicationManagerPersistedEvent;
use App\ApplicationManager\Infrastructure\Cache\ApplicationManagerCacheStore;
use Psr\Log\LoggerInterface;

final class ApplicationManagerPersistedListener
{
    public function __construct(
        private readonly ApplicationManagerCacheStore $cacheStore,
        private readonly LoggerInterface $logger
    ) {
    }

    public function handle(ApplicationManagerPersistedEvent $event): void
    {
        $this->logger->info('ApplicationManager persisted', [
            'application_id' => $event->applicationId,
            'operation' => $event->operation,
        ]);

        if ($event->operation === ApplicationManagerPersistedEvent::OPERATION_DELETED) {
            $this->cacheStore->forget($event->applicationId);

            return;
        }

        $this->cacheStore->warmFromDatabase($event->applicationId);
    }
}
