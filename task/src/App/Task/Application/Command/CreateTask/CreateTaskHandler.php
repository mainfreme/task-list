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
        $dueDate = $command->dueDate
            ? \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $command->dueDate)
                ?: \DateTimeImmutable::createFromFormat('Y-m-d', $command->dueDate)
            : null;

        $task = Task::create(
            $command->title,
            $command->websiteUrl,
            $command->description,
            $command->phone,
            $command->email,
            $command->address,
            $command->applicationManagerId,
            $dueDate,
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
            status: $task->getStatus()->value,
            applicationManagerId: $task->getApplicationManagerId(),
            dueDate: $task->getDueDate()?->format('Y-m-d H:i:s'),
            deliveryAddress: $task->getDeliveryAddress(),
            createdAt: $task->getCreatedAt()->format('Y-m-d H:i:s'),
            updatedAt: $task->getUpdatedAt()->format('Y-m-d H:i:s'),
        );
    }
}
