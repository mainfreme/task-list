<?php

declare(strict_types=1);

namespace App\Auth\Application\Command\LogActivity;

use App\Shared\Domain\ValueObject\Uuid;

final readonly class LogActivityCommand
{
    public function __construct(
        public ?Uuid $userId,
        public string $url,
        public string $ipAddress,
        public string $userAgent,
        public string $action,
        public array $metadata = []
    ) {
    }
}
