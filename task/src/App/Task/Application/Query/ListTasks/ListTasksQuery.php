<?php

declare(strict_types=1);

namespace App\Task\Application\Query\ListTasks;

use App\Task\Domain\ValueObject\ApplicationManagerId;

final class ListTasksQuery
{
    public function __construct(
        public readonly int $page = 1,
        public readonly int $perPage = 20,
        public readonly ?string $status = null,
        public readonly ?ApplicationManagerId $applicationManagerId = null,
    ) {
    }
}
