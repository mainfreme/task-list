<?php

declare(strict_types=1);

namespace Application\Task\DTO;

final class TaskListDTO
{
    /**
     * @param TaskDTO[] $tasks
     */
    public function __construct(
        public readonly array $tasks,
        public readonly int $total,
        public readonly int $page,
        public readonly int $perPage,
        public readonly int $totalPages,
    ) {
    }

    public function toArray(): array
    {
        return [
            'data' => array_map(fn (TaskDTO $task) => $task->toArray(), $this->tasks),
            'meta' => [
                'total' => $this->total,
                'page' => $this->page,
                'per_page' => $this->perPage,
                'total_pages' => $this->totalPages,
            ],
        ];
    }
}

