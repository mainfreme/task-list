<?php

declare(strict_types=1);

namespace App\Task\Application\Query\GetTaskTimeSession;

use App\Task\Application\DTO\TaskTimeSessionStateDto;
use App\Task\Domain\Repository\TaskRepositoryInterface;
use App\Task\Domain\Repository\TaskTimeSessionRepositoryInterface;

final class GetTaskTimeSessionHandler
{
    public function __construct(
        private readonly TaskRepositoryInterface $taskRepository,
        private readonly TaskTimeSessionRepositoryInterface $timeSessionRepository,
    ) {
    }

    public function handle(GetTaskTimeSessionQuery $query): TaskTimeSessionStateDto
    {
        $this->taskRepository->findById($query->taskId);

        $state = $this->timeSessionRepository->getStateForUser($query->taskId, $query->userId);

        return new TaskTimeSessionStateDto(
            isRunning: $state['is_running'],
            currentStartedAt: $state['active_started_at'],
            completedWorkSeconds: $state['total_worked_seconds'],
        );
    }
}
