<?php

declare(strict_types=1);

namespace App\Application\Task\Query\GetTask;

use App\Application\Task\DTO\TaskDTO;
use App\Domain\Task\Repository\TaskRepositoryInterface;

final class GetTaskHandler
{
    public function __construct(
        private readonly TaskRepositoryInterface $repository
    ) {
    }

    public function handle(GetTaskQuery $query): TaskDTO
    {
        $task = $this->repository->findById($query->id);

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

