<?php

declare(strict_types=1);

namespace App\Task\Application\Command\RecordTaskTimeSession;

use App\Task\Application\DTO\TaskTimeSessionStateDto;
use App\Task\Domain\Repository\TaskRepositoryInterface;
use App\Task\Domain\Repository\TaskTimeSessionRepositoryInterface;
use DateTimeImmutable;

final class RecordTaskTimeSessionHandler
{
    public function __construct(
        private readonly TaskRepositoryInterface $taskRepository,
        private readonly TaskTimeSessionRepositoryInterface $timeSessionRepository,
    ) {
    }

    public function handle(RecordTaskTimeSessionCommand $command): TaskTimeSessionStateDto
    {
        $this->taskRepository->findById($command->taskId);

        $now = new DateTimeImmutable('now');

        if ($command->action === 'start') {
            if ($this->timeSessionRepository->findActiveSession($command->taskId, $command->userId) !== null) {
                // Już działa — nie tworzymy drugiego wpisu
            } else {
                $this->timeSessionRepository->startSession($command->taskId, $command->userId, $now);
            }
        } elseif ($command->action === 'pause' || $command->action === 'stop') {
            $this->timeSessionRepository->closeActiveSession($command->taskId, $command->userId, $now);
        }

        $state = $this->timeSessionRepository->getStateForUser($command->taskId, $command->userId);

        return new TaskTimeSessionStateDto(
            isRunning: $state['is_running'],
            currentStartedAt: $state['active_started_at'],
            completedWorkSeconds: $state['total_worked_seconds'],
        );
    }
}
