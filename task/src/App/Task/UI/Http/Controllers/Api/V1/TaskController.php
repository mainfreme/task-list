<?php

declare(strict_types=1);

namespace App\Task\UI\Http\Controllers\Api\V1;

use App\Shared\UI\Http\Controllers\Api\ApiController;
use App\Task\Application\Command\CreateTask\CreateTaskCommand;
use App\Task\Application\Command\CreateTask\CreateTaskHandler;
use App\Task\Application\Command\DeleteTask\DeleteTaskCommand;
use App\Task\Application\Command\DeleteTask\DeleteTaskHandler;
use App\Task\Application\Command\UpdateTask\UpdateTaskCommand;
use App\Task\Application\Command\UpdateTask\UpdateTaskHandler;
use App\Task\Application\Command\UpdateTaskStatus\UpdateTaskStatusCommand;
use App\Task\Application\Command\UpdateTaskStatus\UpdateTaskStatusHandler;
use App\Task\Application\Query\GetTask\GetTaskHandler;
use App\Task\Application\Query\GetTask\GetTaskQuery;
use App\Task\Application\Query\ListTasks\ListTasksHandler;
use App\Task\Application\Query\ListTasks\ListTasksQuery;
use App\Task\Domain\ValueObject\Uuid;
use App\Task\Domain\ValueObject\Title;
use App\Task\Domain\ValueObject\WebsiteUrl;
use App\Task\Domain\ValueObject\Description;
use App\Task\Domain\ValueObject\Phone;
use App\Task\Domain\ValueObject\Email;
use App\Task\Domain\ValueObject\Address;
use App\Task\Domain\ValueObject\ApplicationManagerId;
use App\Task\Domain\ValueObject\DueDate;
use App\Task\Domain\ValueObject\DeliveryAddress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Task\UI\Http\Requests\V1\CreateTaskRequest;
use App\Task\UI\Http\Requests\V1\UpdateTaskRequest;
use App\Task\Domain\Exception\TaskNotFoundException;

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
        $validated = $request->validated();

        $command = new CreateTaskCommand(
            title: Title::fromString($validated['title']),
            websiteUrl: WebsiteUrl::fromString($validated['website_url']),
            description: Description::fromString($validated['description']),
            phone: Phone::fromString($validated['phone']),
            email: Email::fromString($validated['email']),
            address: Address::fromString($validated['address']),
            applicationManagerId: ApplicationManagerId::fromNullable($request->attributes->get('application_manager_id')),
            dueDate: DueDate::fromNullable($validated['due_date'] ?? null),
            deliveryAddress: DeliveryAddress::fromNullable($validated['delivery_address'] ?? null),
        );

        $taskDTO = $this->createTaskHandler->handle($command);

        return $this->created($taskDTO->toArray());
    }

    public function index(Request $request): JsonResponse
    {
        $query = new ListTasksQuery(
            page: (int) $request->input('page', 1),
            perPage: (int) $request->input('per_page', 20),
            status: $request->input('status'),
            applicationManagerId: ApplicationManagerId::fromNullable($request->input('application_manager_id')),
        );

        $result = $this->listTasksHandler->handle($query);

        return $this->success($result->toArray());
    }

    public function show(string $id): JsonResponse
    {
        $query = new GetTaskQuery(id: Uuid::fromString($id));
        $taskDTO = $this->getTaskHandler->handle($query);

        return $this->success($taskDTO->toArray());
    }

    public function update(UpdateTaskRequest $request, string $id): JsonResponse
    {
        $validated = $request->validated();

        $command = new UpdateTaskCommand(
            id: Uuid::fromString($id),
            title: isset($validated['title']) ? Title::fromString($validated['title']) : null,
            websiteUrl: isset($validated['website_url']) ? WebsiteUrl::fromString($validated['website_url']) : null,
            description: isset($validated['description']) ? Description::fromString($validated['description']) : null,
            phone: isset($validated['phone']) ? Phone::fromString($validated['phone']) : null,
            email: isset($validated['email']) ? Email::fromString($validated['email']) : null,
            address: isset($validated['address']) ? Address::fromString($validated['address']) : null,
            dueDate: isset($validated['due_date']) ? DueDate::fromNullable($validated['due_date']) : null,
            deliveryAddress: isset($validated['delivery_address']) ? DeliveryAddress::fromNullable($validated['delivery_address']) : null,
        );

        $taskDTO = $this->updateTaskHandler->handle($command);

        return $this->success($taskDTO->toArray());
    }

    public function updateStatus(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|string|in:pending,in_progress,completed,cancelled',
        ]);

        $command = new UpdateTaskStatusCommand(
            id: Uuid::fromString($id),
            status: TaskStatus::fromString($validated['status']),
        );

        $taskDTO = $this->updateTaskStatusHandler->handle($command);

        return $this->success($taskDTO->toArray());
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $command = new DeleteTaskCommand(id: Uuid::fromString($id));
            $this->deleteTaskHandler->handle($command);

            return $this->success(['message' => 'Task deleted successfully']);
        } catch (TaskNotFoundException $e) {
            return $this->notFound($e->getMessage());
        }
    }
}
