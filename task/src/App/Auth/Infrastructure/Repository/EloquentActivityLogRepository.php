<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Repository;

use App\Auth\Domain\Entity\ActivityUserLog;
use App\Auth\Domain\Repository\ActivityLogRepositoryInterface;
use App\Auth\Infrastructure\Model\ActivityUserLogModel;
use App\Shared\Domain\ValueObject\Uuid;

final class EloquentActivityLogRepository implements ActivityLogRepositoryInterface
{
    public function save(ActivityUserLog $activityLog): void
    {
        $model = $activityLog->getId()
            ? ActivityUserLogModel::find($activityLog->getId())
            : new ActivityUserLogModel();

        if (!$model) {
            $model = new ActivityUserLogModel();
        }

        $model->user_id = $activityLog->getUserId()?->getValue();
        $model->url = $activityLog->getUrl();
        $model->log_activity = $activityLog->getLogActivity();

        $model->save();

        if ($activityLog->getId() === null) {
            $activityLog->setId($model->id);
        }
    }

    public function findById(int $id): ?ActivityUserLog
    {
        $model = ActivityUserLogModel::find($id);

        if (!$model) {
            return null;
        }

        return $this->toEntity($model);
    }

    public function findByUserId(Uuid $userId): array
    {
        $models = ActivityUserLogModel::where('user_id', $userId->getValue())
            ->orderBy('created_at', 'desc')
            ->get();

        return $models->map(fn (ActivityUserLogModel $model) => $this->toEntity($model))->all();
    }

    private function toEntity(ActivityUserLogModel $model): ActivityUserLog
    {
        return ActivityUserLog::fromDatabase(
            id: $model->id,
            userId: $model->user_id ? Uuid::fromString($model->user_id) : null,
            url: $model->url,
            logActivity: $model->log_activity ?? [],
            createdAt: $model->created_at ? \DateTimeImmutable::createFromMutable($model->created_at) : null,
            updatedAt: $model->updated_at ? \DateTimeImmutable::createFromMutable($model->updated_at) : null
        );
    }
}
