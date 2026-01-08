<?php

declare(strict_types=1);

namespace App\Task\Application\Command\UpdateTaskStatus;

use App\Task\Application\DTO\TaskDTO;
use App\Task\Domain\Repository\TaskRepositoryInterface;
use App\Task\Domain\ValueObject\TaskStatus;

final class UpdateTaskStatusHandler
{
    public function __construct(
        private readonly TaskRepositoryInterface $repository
    ) {
    }

    public function handle(UpdateTaskStatusCommand $command): TaskDTO
    {
        $task = $this->repository->findById($command->id);

        $status = TaskStatus::fromString($command->status);
        $task->setStatus($status);

        $this->repository->save($task);

        return new TaskDTO(
            id: $task->getId(),
            title: $task->getTitle(),
            websiteUrl: $task->getWebsiteUrl(),
            description: $task->getDescription(),
            phone: $task->getPhone(),
            email: $task->getEmail(),
            address: $task->getAddress(),
            status: $task->getStatus()->value,
            applicationManagerId: $task->getApplicationManagerId(),
            dueDate: $task->getDueDate()?->format('Y-m-d H:i:s'),
            deliveryAddress: $task->getDeliveryAddress(),
            createdAt: $task->getCreatedAt()->format('Y-m-d H:i:s'),
            updatedAt: $task->getUpdatedAt()->format('Y-m-d H:i:s'),
        );
    }
}
