<?php

namespace App\Task\Application\Query\ListTasks;

use App\Task\Domain\Repository\TaskRepositoryInterface;
use App\Task\Application\DTO\TaskStatusesCountDTO;
use App\Task\Domain\DTO\Stats\CountStatusesTaskDto;


final class CountStatusesTaskHandler
{
    public function __construct(
        private readonly TaskRepositoryInterface $repository,
    ) {
    }

    public function handle(CountStatusesTaskDto $dto): TaskStatusesCountDTO
    {
        $countTasksStatuses = $this->repository->groupByStatus($dto);

        return new TaskStatusesCountDTO($countTasksStatuses);
    }
}
