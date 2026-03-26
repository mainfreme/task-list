<?php

declare(strict_types=1);

namespace App\Task\Application\Command\RecordTaskTimeSession;

use App\Shared\Domain\ValueObject\Uuid;

final readonly class RecordTaskTimeSessionCommand
{
    public function __construct(
        public Uuid $taskId,
        public Uuid $userId,
        /** @var 'start'|'pause'|'stop' */
        public string $action,
    ) {
    }
}
