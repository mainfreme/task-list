<?php

declare(strict_types=1);

namespace App\Application\Task\Command\DeleteTask;

use App\Domain\Task\Repository\TaskRepositoryInterface;
use App\Domain\Task\Exception\TaskNotFoundException;

final class DeleteTaskHandler
{
    public function __construct(private readonly TaskRepositoryInterface $repository) {}

    public function handle(DeleteTaskCommand $command): void
    {
        $task = $this->repository->findById($command->id);

        if (!$task) {
            throw new TaskNotFoundException();
        }

        $this->repository->softDelete($task);
    }
}