<?php

declare(strict_types=1);

namespace Application\Task\Query\ListTasks;

final class ListTasksQuery
{
    public function __construct(
        public readonly int $page = 1,
        public readonly int $perPage = 20,
        public readonly ?string $status = null,
        public readonly ?int $applicationManagerId = null,
    ) {
    }
}

