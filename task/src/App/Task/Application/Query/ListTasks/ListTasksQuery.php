<?php

declare(strict_types=1);

namespace App\Task\Application\Query\ListTasks;

use App\Task\Domain\ValueObject\ApplicationManagerId;

final class ListTasksQuery
{
    /**
     * @param list<ApplicationManagerId>              $applicationManagerIds puste = brak filtra
     * @param list<\App\Shared\Domain\ValueObject\Uuid> $userIds puste = brak filtra
     */
    public function __construct(
        public readonly int $page = 1,
        public readonly int $perPage = 20,
        public readonly ?string $status = null,
        public readonly array $applicationManagerIds = [],
        public readonly array $userIds = [],
        public readonly string $sortBy = 'created_at',
        public readonly string $sortDir = 'desc',
    ) {
    }
}
