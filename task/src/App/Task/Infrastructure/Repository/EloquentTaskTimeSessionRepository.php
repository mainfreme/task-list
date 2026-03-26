<?php

declare(strict_types=1);

namespace App\Task\Infrastructure\Repository;

use App\Shared\Domain\ValueObject\Uuid;
use App\Task\Domain\Repository\TaskTimeSessionRepositoryInterface;
use App\Task\Infrastructure\Model\TaskTimeSessionModel;
use Carbon\Carbon;
use DateTimeImmutable;
use Illuminate\Support\Str;

final class EloquentTaskTimeSessionRepository implements TaskTimeSessionRepositoryInterface
{
    public function findActiveSession(Uuid $taskId, Uuid $userId): ?array
    {
        $row = TaskTimeSessionModel::query()
            ->where('task_id', $taskId->getValue())
            ->where('user_id', $userId->getValue())
            ->whereNull('ended_at')
            ->first();

        if (!$row) {
            return null;
        }

        return [
            'id' => $row->id,
            'started_at' => $row->started_at?->toIso8601String(),
        ];
    }

    public function getStateForUser(Uuid $taskId, Uuid $userId): array
    {
        $rows = TaskTimeSessionModel::query()
            ->where('task_id', $taskId->getValue())
            ->where('user_id', $userId->getValue())
            ->orderBy('started_at')
            ->get();

        $closedSeconds = 0;
        $activeStartedAt = null;

        foreach ($rows as $row) {
            $start = $row->started_at;
            if (!$start) {
                continue;
            }
            if ($row->ended_at === null) {
                $activeStartedAt = $start->toIso8601String();
            } else {
                $closedSeconds += max(0, Carbon::parse($start)->diffInSeconds(Carbon::parse($row->ended_at)));
            }
        }

        return [
            'total_worked_seconds' => $closedSeconds,
            'active_started_at' => $activeStartedAt,
            'is_running' => $activeStartedAt !== null,
        ];
    }

    public function startSession(Uuid $taskId, Uuid $userId, DateTimeImmutable $startedAt): void
    {
        TaskTimeSessionModel::query()->create([
            'id' => (string) Str::uuid(),
            'task_id' => $taskId->getValue(),
            'user_id' => $userId->getValue(),
            'started_at' => $startedAt->format('Y-m-d H:i:s'),
            'ended_at' => null,
        ]);
    }

    public function closeActiveSession(Uuid $taskId, Uuid $userId, DateTimeImmutable $endedAt): bool
    {
        $row = TaskTimeSessionModel::query()
            ->where('task_id', $taskId->getValue())
            ->where('user_id', $userId->getValue())
            ->whereNull('ended_at')
            ->first();

        if (!$row) {
            return false;
        }

        $row->ended_at = $endedAt->format('Y-m-d H:i:s');
        $row->save();

        return true;
    }
}
