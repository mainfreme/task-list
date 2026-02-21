<?php

declare(strict_types=1);

namespace App\Task\Application\Command\DeleteTask;

use App\Task\Domain\Repository\TaskRepositoryInterface;
use App\Task\Domain\Exception\TaskNotFoundException;

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
