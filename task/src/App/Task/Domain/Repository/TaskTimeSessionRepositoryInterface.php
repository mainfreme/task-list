<?php

declare(strict_types=1);

namespace App\Task\Domain\Repository;

use App\Shared\Domain\ValueObject\Uuid;
use DateTimeImmutable;

interface TaskTimeSessionRepositoryInterface
{
    public function findActiveSession(Uuid $taskId, Uuid $userId): ?array;

    /**
     * @return array{total_worked_seconds: int, active_started_at: ?string, is_running: bool}
     */
    public function getStateForUser(Uuid $taskId, Uuid $userId): array;

    public function startSession(Uuid $taskId, Uuid $userId, DateTimeImmutable $startedAt): void;

    public function closeActiveSession(Uuid $taskId, Uuid $userId, DateTimeImmutable $endedAt): bool;
}
