<?php

declare(strict_types=1);

namespace App\UI\Http\Controllers\Api\V1;

use App\Application\Task\Command\CreateTask\CreateTaskCommand;
use App\Application\Task\Command\CreateTask\CreateTaskHandler;
use App\Application\Task\Command\UpdateTask\UpdateTaskCommand;
use App\Application\Task\Command\UpdateTask\UpdateTaskHandler;
use App\Application\Task\Command\UpdateTaskStatus\UpdateTaskStatusCommand;
use App\Application\Task\Command\UpdateTaskStatus\UpdateTaskStatusHandler;
use App\Application\Task\Query\GetTask\GetTaskHandler;
use App\Application\Task\Query\GetTask\GetTaskQuery;
use App\Application\Task\Query\ListTasks\ListTasksHandler;
use App\Application\Task\Query\ListTasks\ListTasksQuery;
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

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'website_url' => 'required|string|url|max:255',
            'description' => 'required|string',
            'phone' => 'required|string|max:50',
            'email' => 'required|email|max:255',
            'address' => 'required|string',
            'due_date' => 'nullable|date',
            'delivery_address' => 'nullable|string',
        ]);

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

    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'website_url' => 'sometimes|string|url|max:255',
            'description' => 'sometimes|string',
            'phone' => 'sometimes|string|max:50',
            'email' => 'sometimes|email|max:255',
            'address' => 'sometimes|string',
            'due_date' => 'nullable|date',
            'delivery_address' => 'nullable|string',
        ]);

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

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|string|in:pending,in_progress,completed,cancelled',
        ]);

        $command = new UpdateTaskStatusCommand(
            id: $id,
            status: $validated['status'],
        );

        $taskDTO = $this->updateTaskStatusHandler->handle($command);

        return response()->json($taskDTO->toArray());
    }
}

