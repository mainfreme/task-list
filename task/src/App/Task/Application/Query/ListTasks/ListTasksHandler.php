<?php

declare(strict_types=1);

namespace App\Task\Application\Query\ListTasks;

use App\Task\Application\DTO\TaskDTO;
use App\Task\Application\DTO\TaskListDTO;
use App\Task\Domain\Entity\Task;
use App\Task\Domain\Repository\TaskRepositoryInterface;
use App\Task\Domain\ValueObject\TaskStatus;

final class ListTasksHandler
{
    public function __construct(
        private readonly TaskRepositoryInterface $repository
    ) {
    }

    public function handle(ListTasksQuery $query): TaskListDTO
    {
        $offset = ($query->page - 1) * $query->perPage;

        $status = $query->status !== null ? TaskStatus::fromString($query->status) : null;

        $tasks = $this->repository->findForList(
            $status,
            $query->applicationManagerIds,
            $query->userIds,
            $query->perPage,
            $offset,
            $query->sortBy,
            $query->sortDir,
        );

        $total = $this->repository->countForList($status, $query->applicationManagerIds, $query->userIds);
        $totalPages = $query->perPage > 0 ? (int) ceil($total / $query->perPage) : 0;

        $taskDTOs = array_map(
            fn (Task $task) => new TaskDTO(
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
                createdAt: $task->getCreatedAt(),
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
