<?php

declare(strict_types=1);

namespace App\Task\Application\Command\UpdateTaskStatus;

use App\Shared\Domain\ValueObject\Uuid;
use App\Task\Domain\ValueObject\TaskStatus;

final class UpdateTaskStatusCommand
{
    public function __construct(
        public readonly Uuid $id,
        public readonly TaskStatus $status,
    ) {
    }
}
