<?php

declare(strict_types=1);

namespace App\Auth\Domain\Service;

use App\Shared\Domain\ValueObject\Uuid;

interface ActivityLogProducerInterface
{
    public function log(
        ?Uuid $userId,
        string $url,
        string $ipAddress,
        string $userAgent,
        string $action,
        array $metadata = []
    ): void;
}
