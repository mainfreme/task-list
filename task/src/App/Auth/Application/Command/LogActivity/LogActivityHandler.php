<?php

declare(strict_types=1);

namespace App\Auth\Application\Command\LogActivity;

use App\Auth\Domain\Service\ActivityLogProducerInterface;

final class LogActivityHandler
{
    public function __construct(
        private readonly ActivityLogProducerInterface $activityLogProducer
    ) {
    }

    public function handle(LogActivityCommand $command): void
    {
        $this->activityLogProducer->log(
            userId: $command->userId,
            url: $command->url,
            ipAddress: $command->ipAddress,
            userAgent: $command->userAgent,
            action: $command->action,
            metadata: $command->metadata
        );
    }
}
