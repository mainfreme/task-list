<?php

declare(strict_types=1);

namespace App\Task\Application\Query\GetTask;

use App\Task\Application\DTO\TaskDTO;
use App\Task\Domain\Repository\TaskRepositoryInterface;

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
            status: $task->getStatus(),
            applicationManagerId: $task->getApplicationManagerId(),
            userId: $task->getUserId(),
            dueDate: $task->getDueDate(),
            deliveryAddress: $task->getDeliveryAddress(),
            createdAt: $task->getCreatedAt()
        );
    }
}
