<?php

declare(strict_types=1);

namespace Application\Task\Command\UpdateTask;

use Application\Task\DTO\TaskDTO;
use Domain\Task\Repository\TaskRepositoryInterface;

final class UpdateTaskHandler
{
    public function __construct(
        private readonly TaskRepositoryInterface $repository
    ) {
    }

    public function handle(UpdateTaskCommand $command): TaskDTO
    {
        $task = $this->repository->findById($command->id);

        if ($command->title !== null) {
            $task->setTitle($command->title);
        }

        if ($command->websiteUrl !== null) {
            $task->setWebsiteUrl($command->websiteUrl);
        }

        if ($command->description !== null) {
            $task->setDescription($command->description);
        }

        if ($command->phone !== null) {
            $task->setPhone($command->phone);
        }

        if ($command->email !== null) {
            $task->setEmail($command->email);
        }

        if ($command->address !== null) {
            $task->setAddress($command->address);
        }

        if ($command->dueDate !== null) {
            $dueDate = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $command->dueDate)
                ?: \DateTimeImmutable::createFromFormat('Y-m-d', $command->dueDate);
            if ($dueDate) {
                $task->setDueDate($dueDate);
            }
        }

        if ($command->deliveryAddress !== null) {
            $task->setDeliveryAddress($command->deliveryAddress);
        }

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

