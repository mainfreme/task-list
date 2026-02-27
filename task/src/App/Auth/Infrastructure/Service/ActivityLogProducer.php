<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Service;

use App\Auth\Domain\Service\ActivityLogProducerInterface;
use App\Shared\Domain\ValueObject\Uuid;
use App\Shared\Infrastructure\MessageBroker\MessageProducerInterface;
use App\Shared\Infrastructure\MessageBroker\Queue;

final class ActivityLogProducer implements ActivityLogProducerInterface
{
    public function __construct(
        private readonly MessageProducerInterface $producer
    ) {
    }

    public function log(
        ?Uuid $userId,
        string $url,
        string $ipAddress,
        string $userAgent,
        string $action,
        array $metadata = []
    ): void {
        $this->producer->publish(
            message: [
                'user_id' => $userId?->getValue(),
                'url' => $url,
                'log_activity' => [
                    'ip' => $ipAddress,
                    'user_agent' => $userAgent,
                    'action' => $action,
                    'metadata' => $metadata,
                    'logged_at' => (new \DateTimeImmutable())->format('c'),
                ],
            ],
            queue: Queue::ACTIVITY_LOGS
        );
    }
}
