<?php

declare(strict_types=1);

namespace App\Auth\Domain\Repository;

use App\Auth\Domain\Entity\ActivityUserLog;
use App\Shared\Domain\ValueObject\Uuid;

interface ActivityLogRepositoryInterface
{
    public function save(ActivityUserLog $activityLog): void;

    public function findById(int $id): ?ActivityUserLog;

    public function findByUserId(Uuid $userId): array;
}
