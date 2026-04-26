<?php

declare(strict_types=1);

namespace App\Task\Application\Command\CreateTask;

use App\Task\Application\DTO\TaskDTO;
use App\Task\Domain\Entity\Task;
use App\Task\Domain\Repository\TaskRepositoryInterface;

final class CreateTaskHandler
{
    public function __construct(
        private readonly TaskRepositoryInterface $repository
    ) {
    }

    public function handle(CreateTaskCommand $command): TaskDTO
    {
        $task = Task::create(
            $command->title,
            $command->websiteUrl,
            $command->description,
            $command->phone,
            $command->email,
            $command->address,
            $command->applicationManagerId,
            $command->dueDate,
            $command->deliveryAddress
        );

        $this->repository->save($task);

        return new TaskDTO(
            id: $task->getId(),
            title: $task->getTitle(),
            websiteUrl: $task->getWebsiteUrl(),
            description: $task->getDescription(),
            phone: $task->getPhone(),
            email: $task->getEmail(),
            address: $task->getAddress(),
            status: $task->getStatus(),
            applicationManagerId: $task->getApplicationManagerId(),
            userId: $task->getUserId(),
            dueDate: $task->getDueDate(),
            deliveryAddress: $task->getDeliveryAddress(),
            createdAt: $task->getCreatedAt()
        );
    }
}
