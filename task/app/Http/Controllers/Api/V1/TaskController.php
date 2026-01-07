<?php

declare(strict_types=1);

namespace App\UI\Http\Controllers\Api\V1;

use App\UI\Http\Controllers\Api\ApiController;
use App\Application\Task\Command\CreateTask\CreateTaskCommand;
use App\Application\Task\Command\CreateTask\CreateTaskHandler;
use App\Application\Task\Command\DeleteTask\DeleteTaskCommand;
use App\Application\Task\Command\DeleteTask\DeleteTaskHandler;
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
use App\UI\Http\Requests\V1\CreateTaskRequest;
use App\UI\Http\Requests\V1\UpdateTaskRequest;
use App\Domain\Task\Exception\TaskNotFoundException;
use Illuminate\Validation\ValidationException;

final class TaskController extends ApiController
{
    public function __construct(
        private readonly CreateTaskHandler $createTaskHandler,
        private readonly UpdateTaskHandler $updateTaskHandler,
        private readonly UpdateTaskStatusHandler $updateTaskStatusHandler,
        private readonly DeleteTaskHandler $deleteTaskHandler,
        private readonly GetTaskHandler $getTaskHandler,
        private readonly ListTasksHandler $listTasksHandler,
    ) {
    }

    public function store(CreateTaskRequest $request): JsonResponse
    {
        // try {
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

            return $this->created($taskDTO->toArray());
        // } catch (ValidationException $e) {
        //     return $this->badRequest($e->getMessage(), $e->validator->errors());
        // }
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

        return $this->success($result->toArray());
    }

    public function show(int $id): JsonResponse
    {
        $query = new GetTaskQuery(id: $id);
        $taskDTO = $this->getTaskHandler->handle($query);

        return $this->success($taskDTO->toArray());
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

        return $this->success($taskDTO->toArray());
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

        return $this->success($taskDTO->toArray());
    }

    public function destroy(int $id): JsonResponse
    {
        try {
        $command = new DeleteTaskCommand(id: $id);
        $this->deleteTaskHandler->handle($command);

            return $this->success(['message' => 'Task deleted successfully']);
        } catch (TaskNotFoundException $e) {
            return $this->notFound($e->getMessage());
        }
    }


}

