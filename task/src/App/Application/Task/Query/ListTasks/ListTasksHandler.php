<?php

declare(strict_types=1);

namespace App\Application\Task\Query\ListTasks;

use App\Application\Task\DTO\TaskDTO;
use App\Application\Task\DTO\TaskListDTO;
use App\Domain\Task\Repository\TaskRepositoryInterface;
use App\Domain\Task\ValueObject\TaskStatus;

final class ListTasksHandler
{
    public function __construct(
        private readonly TaskRepositoryInterface $repository
    ) {
    }

    public function handle(ListTasksQuery $query): TaskListDTO
    {
        $offset = ($query->page - 1) * $query->perPage;

        if ($query->status !== null) {
            $status = TaskStatus::fromString($query->status);
            $tasks = $this->repository->findByStatus($status);
        } elseif ($query->applicationManagerId !== null) {
            $tasks = $this->repository->findByApplicationId($query->applicationManagerId);
        } else {
            $tasks = $this->repository->findAll($query->perPage, $offset);
        }

        $total = $this->repository->count();
        $totalPages = (int) ceil($total / $query->perPage);

        $taskDTOs = array_map(
            fn ($task) => new TaskDTO(
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
            ),
            $tasks
        );

        return new TaskListDTO(
            tasks: $taskDTOs,
            total: $total,
            page: $query->page,
            perPage: $query->perPage,
            totalPages: $totalPages,
        );
    }
}

