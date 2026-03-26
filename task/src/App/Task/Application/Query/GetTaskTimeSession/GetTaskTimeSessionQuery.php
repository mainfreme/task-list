<?php

declare(strict_types=1);

namespace App\Task\Application\Query\GetTaskTimeSession;

use App\Shared\Domain\ValueObject\Uuid;

final readonly class GetTaskTimeSessionQuery
{
    public function __construct(
        public Uuid $taskId,
        public Uuid $userId,
    ) {
    }
}
