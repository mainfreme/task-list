<?php

declare(strict_types=1);

namespace App\UI\Http\Controllers\Api\V1;

use Application\Task\Command\CreateTask\CreateTaskCommand;
use Application\Task\Command\CreateTask\CreateTaskHandler;
use Application\Task\Command\UpdateTask\UpdateTaskCommand;
use Application\Task\Command\UpdateTask\UpdateTaskHandler;
use Application\Task\Command\UpdateTaskStatus\UpdateTaskStatusCommand;
use Application\Task\Command\UpdateTaskStatus\UpdateTaskStatusHandler;
use Application\Task\Query\GetTask\GetTaskQuery;
use Application\Task\Query\GetTask\GetTaskHandler;
use Application\Task\Query\ListTasks\ListTasksQuery;
use Application\Task\Query\ListTasks\ListTasksHandler;
use App\UI\Http\Requests\Api\V1\StoreTaskRequest;
use App\UI\Http\Requests\Api\V1\UpdateTaskRequest;
use App\UI\Http\Requests\Api\V1\UpdateTaskStatusRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class TaskController
{
    public function __construct(
        private readonly CreateTaskHandler $createTaskHandler,
        private readonly UpdateTaskHandler $updateTaskHandler,
        private readonly UpdateTaskStatusHandler $updateTaskStatusHandler,
        private readonly GetTaskHandler $getTaskHandler,
        private readonly ListTasksHandler $listTasksHandler,
    ) {
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $command = new CreateTaskCommand(
            title: $validated['title'],
            websiteUrl: $validated['website_url'],
            description: $validated['description'],
            phone: $validated['phone'],
            email: $validated['email'],
            address: $validated['address'],
            applicationManagerId: $request->attributes->get('application_manager_id'), // Set by ApiKeyMiddleware
            dueDate: $validated['due_date'] ?? null,
            deliveryAddress: $validated['delivery_address'] ?? null,
        );

        $taskDTO = $this->createTaskHandler->handle($command);

        return response()->json($taskDTO->toArray(), 201);
    }

    public function index(Request $request): JsonResponse
    {
        $query = new ListTasksQuery(
            page: (int) $request->input('page', 1),
            perPage: (int) $request->input('per_page', 20),
            status: $request->input('status'),
            applicationManagerId: $request->input('application_manager_id'),
        );

        $result = $this->listTasksHandler->handle($query);

        return response()->json($result->toArray());
    }

    public function show(int $id): JsonResponse
    {
        $query = new GetTaskQuery(id: $id);
        $taskDTO = $this->getTaskHandler->handle($query);

        return response()->json($taskDTO->toArray());
    }

    public function update(UpdateTaskRequest $request, int $id): JsonResponse
    {
        $validated = $request->validated();

        $command = new UpdateTaskCommand(
            id: $id,
            title: $validated['title'] ?? null,
            websiteUrl: $validated['website_url'] ?? null,
            description: $validated['description'] ?? null,
            phone: $validated['phone'] ?? null,
            email: $validated['email'] ?? null,
            address: $validated['address'] ?? null,
            dueDate: $validated['due_date'] ?? null,
            deliveryAddress: $validated['delivery_address'] ?? null,
        );

        $taskDTO = $this->updateTaskHandler->handle($command);

        return response()->json($taskDTO->toArray());
    }

    public function updateStatus(UpdateTaskStatusRequest $request, int $id): JsonResponse
    {
        $validated = $request->validated();

        $command = new UpdateTaskStatusCommand(
            id: $id,
            status: $validated['status'],
        );

        $taskDTO = $this->updateTaskStatusHandler->handle($command);

        return response()->json($taskDTO->toArray());
    }
}

