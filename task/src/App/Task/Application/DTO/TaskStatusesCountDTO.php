<?php

namespace App\Task\Application\DTO;

final class TaskStatusesCountDTO
{
    public function __construct(
        public readonly array $countTasksStatuses
    ) {
    }

    /**
     * @return array[]
     */
    public function toArray(): array
    {
        return [
            'data' => $this->countTasksStatuses,
        ];
    }
}
