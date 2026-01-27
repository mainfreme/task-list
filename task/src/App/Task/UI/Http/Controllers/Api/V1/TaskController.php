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
use App\Task\Domain\ValueObject\TaskStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Task\UI\Http\Requests\V1\CreateTaskRequest;
use App\Task\UI\Http\Requests\V1\UpdateTaskRequest;
use App\Task\Domain\Exception\TaskNotFoundException;
use OpenApi\Attributes as OA;

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

    #[OA\Post(
        path: "/v1/tasks",
        summary: "Create a new task",
        tags: ["Tasks"],
        security: [["jwt" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["title", "website_url", "description", "address"],
                properties: [
                    new OA\Property(property: "title", type: "string", example: "Fix bug"),
                    new OA\Property(property: "website_url", type: "string", format: "url", example: "https://example.com"),
                    new OA\Property(property: "description", type: "string", example: "Fix the critical bug"),
                    new OA\Property(property: "phone", type: "string", example: "+1234567890"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "john@example.com"),
                    new OA\Property(property: "address", type: "string", example: "123 Main St"),
                    new OA\Property(property: "due_date", type: "string", format: "date", example: "2023-12-31"),
                    new OA\Property(property: "delivery_address", type: "string", example: "456 Side St"),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Task created successfully"),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
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

    #[OA\Get(
        path: "/v1/tasks",
        summary: "List tasks",
        tags: ["Tasks"],
        security: [["jwt" => []]],
        parameters: [
            new OA\Parameter(name: "page", in: "query", description: "Page number", schema: new OA\Schema(type: "integer", default: 1)),
            new OA\Parameter(name: "per_page", in: "query", description: "Items per page", schema: new OA\Schema(type: "integer", default: 20)),
            new OA\Parameter(name: "status", in: "query", description: "Filter by status", schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "application_manager_id", in: "query", description: "Filter by Application Manager ID", schema: new OA\Schema(type: "string", format: "uuid"))
        ],
        responses: [
            new OA\Response(response: 200, description: "List of tasks")
        ]
    )]
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

    #[OA\Get(
        path: "/v1/tasks/{id}",
        summary: "Get task details",
        tags: ["Tasks"],
        security: [["jwt" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "Task ID", schema: new OA\Schema(type: "string", format: "uuid"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Task details"),
            new OA\Response(response: 404, description: "Task not found")
        ]
    )]
    public function show(string $id): JsonResponse
    {
        $query = new GetTaskQuery(id: Uuid::fromString($id));
        $taskDTO = $this->getTaskHandler->handle($query);

        return $this->success($taskDTO->toArray());
    }

    #[OA\Put(
        path: "/v1/tasks/{id}",
        summary: "Update task",
        tags: ["Tasks"],
        security: [["jwt" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "Task ID", schema: new OA\Schema(type: "string", format: "uuid"))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "title", type: "string", example: "Fix bug updated"),
                    new OA\Property(property: "website_url", type: "string", format: "url", example: "https://example.com"),
                    new OA\Property(property: "description", type: "string", example: "Updated description"),
                    new OA\Property(property: "phone", type: "string", example: "+1234567890"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "john@example.com"),
                    new OA\Property(property: "address", type: "string", example: "123 Main St"),
                    new OA\Property(property: "due_date", type: "string", format: "date", example: "2023-12-31"),
                    new OA\Property(property: "delivery_address", type: "string", example: "456 Side St"),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Task updated successfully"),
            new OA\Response(response: 404, description: "Task not found")
        ]
    )]
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

    #[OA\Patch(
        path: "/v1/tasks/{id}/status",
        summary: "Update task status",
        tags: ["Tasks"],
        security: [["jwt" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "Task ID", schema: new OA\Schema(type: "string", format: "uuid"))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["status"],
                properties: [
                    new OA\Property(property: "status", type: "string", enum: ["pending", "in_progress", "completed", "cancelled"], example: "in_progress")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Task status updated"),
            new OA\Response(response: 404, description: "Task not found")
        ]
    )]
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

    #[OA\Delete(
        path: "/v1/tasks/{id}",
        summary: "Delete task",
        tags: ["Tasks"],
        security: [["jwt" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "Task ID", schema: new OA\Schema(type: "string", format: "uuid"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Task deleted successfully"),
            new OA\Response(response: 404, description: "Task not found")
        ]
    )]
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
