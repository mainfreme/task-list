<?php

declare(strict_types=1);

namespace App\Task\Domain\Repository;

use App\Shared\Domain\ValueObject\Uuid;
use App\Task\Domain\Entity\Task;
use App\Task\Domain\Exception\TaskNotFoundException;
use App\Task\Domain\ValueObject\ApplicationManagerId;
use App\Task\Domain\ValueObject\TaskStatus;

interface TaskRepositoryInterface
{
    /**
     * @throws TaskNotFoundException
     */
    public function findById(Uuid $id): Task;

    /**
     * @return Task[]
     */
    public function findByApplicationId(ApplicationManagerId $applicationId): array;

    /**
     * @return Task[]
     */
    public function findByStatus(TaskStatus $status): array;

    /**
     * @return Task[]
     */
    public function findAll(int $limit = 50, int $offset = 0): array;

    public function count(): int;

    public function save(Task $task): void;

    public function delete(Task $task): void;

    public function softDelete(Task $task): void;
}
