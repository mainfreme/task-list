<?php

declare(strict_types=1);

namespace App\Task\UI\Http\Controllers\Api\V1;

use App\Shared\Domain\ValueObject\Uuid;
use App\Shared\UI\Http\Controllers\Api\ApiController;
use App\Task\Application\Command\RecordTaskTimeSession\RecordTaskTimeSessionCommand;
use App\Task\Application\Command\RecordTaskTimeSession\RecordTaskTimeSessionHandler;
use App\Task\Application\Query\GetTaskTimeSession\GetTaskTimeSessionHandler;
use App\Task\Application\Query\GetTaskTimeSession\GetTaskTimeSessionQuery;
use App\Task\UI\Http\Requests\V1\RecordTaskTimeSessionRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

final class TaskTimeController extends ApiController
{
    public function __construct(
        private readonly GetTaskTimeSessionHandler $getTaskTimeSessionHandler,
        private readonly RecordTaskTimeSessionHandler $recordTaskTimeSessionHandler,
    ) {
    }

    #[OA\Get(
        path: '/v1/tasks/{id}/time',
        summary: 'Stan licznika czasu dla zadania i zalogowanego użytkownika',
        tags: ['Tasks'],
        security: [['jwt' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Stan czasu'),
            new OA\Response(response: 404, description: 'Task not found'),
        ]
    )]
    public function show(Request $request, string $id): JsonResponse
    {
        /** @var Uuid $userId */
        $userId = $request->attributes->get('user_id');
        $dto = $this->getTaskTimeSessionHandler->handle(new GetTaskTimeSessionQuery(
            taskId: Uuid::fromString($id),
            userId: $userId,
        ));

        return $this->success($dto->toArray());
    }

    #[OA\Post(
        path: '/v1/tasks/{id}/time',
        summary: 'Start / pauza / stop sesji czasu pracy przy zadaniu',
        tags: ['Tasks'],
        security: [['jwt' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['action'],
                properties: [
                    new OA\Property(property: 'action', type: 'string', enum: ['start', 'pause', 'stop']),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Zaktualizowany stan'),
            new OA\Response(response: 404, description: 'Task not found'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function store(RecordTaskTimeSessionRequest $request, string $id): JsonResponse
    {
        /** @var Uuid $userId */
        $userId = $request->attributes->get('user_id');
        $dto = $this->recordTaskTimeSessionHandler->handle(new RecordTaskTimeSessionCommand(
            taskId: Uuid::fromString($id),
            userId: $userId,
            action: $request->validated('action'),
        ));

        return $this->success($dto->toArray());
    }
}
