<?php

declare(strict_types=1);

namespace App\Domain\Task\Repository;

use App\Domain\Task\Entity\Task;
use App\Domain\Task\Exception\TaskNotFoundException;
use App\Domain\Task\ValueObject\TaskStatus;

interface TaskRepositoryInterface
{
    /**
     * @throws TaskNotFoundException
     */
    public function findById(int $id): Task;

    /**
     * @return Task[]
     */
    public function findByApplicationId(int $applicationId): array;

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
}

