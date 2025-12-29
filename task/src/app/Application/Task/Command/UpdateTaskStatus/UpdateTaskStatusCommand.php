<?php

declare(strict_types=1);

namespace App\Application\Task\Command\UpdateTaskStatus;

final class UpdateTaskStatusCommand
{
    public function __construct(
        public readonly int $id,
        public readonly string $status,
    ) {
    }
}

