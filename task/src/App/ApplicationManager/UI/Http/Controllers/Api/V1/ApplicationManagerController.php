<?php

declare(strict_types=1);

namespace App\ApplicationManager\UI\Http\Controllers\Api\V1;

use App\ApplicationManager\Application\Command\CreateApplicationManager\CreateApplicationManagerHandler;
use App\ApplicationManager\Application\Command\GenerateApiKey\GenerateApiKeyCommand;
use App\ApplicationManager\Application\Command\GenerateApiKey\GenerateApiKeyHandler;
use App\ApplicationManager\Application\Command\GenerateJwtToken\GenerateJwtTokenHandler;
use App\ApplicationManager\Application\Command\UpdateApplicationManager\ChangeStatusCommand;
use App\ApplicationManager\Application\Command\UpdateApplicationManager\ChangeStatusHandler;
use App\ApplicationManager\Application\Command\UpdateApplicationManager\UpdateApplicationManagerHandler;
use App\ApplicationManager\Application\Query\GetApplicationManager\GetApplicationManagerHandler;
use App\ApplicationManager\Application\Query\GetApplicationManager\GetApplicationManagerQuery;
use App\ApplicationManager\Application\Query\ListApplicationManagers\ListApplicationManagersHandler;
use App\ApplicationManager\UI\Http\Mappers\CreateApplicationManagerCommandMapper;
use App\ApplicationManager\UI\Http\Mappers\GenerateJwtTokenCommandMapper;
use App\ApplicationManager\UI\Http\Mappers\ListApplicationManagersQueryMapper;
use App\ApplicationManager\UI\Http\Mappers\UpdateApplicationManagerCommandMapper;
use App\ApplicationManager\UI\Http\Requests\V1\ChangeStatusRequest;
use App\ApplicationManager\UI\Http\Requests\V1\CreateApplicationManagerRequest;
use App\ApplicationManager\UI\Http\Requests\V1\GenerateJwtTokenRequest;
use App\ApplicationManager\UI\Http\Requests\V1\UpdateApplicationManagerRequest;
use App\Shared\Domain\ValueObject\Uuid;
use App\Shared\Infrastructure\Mapper\GenericRequestMapper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

final class ApplicationManagerController
{
    public function __construct(
        private readonly CreateApplicationManagerHandler $createHandler,
        private readonly UpdateApplicationManagerHandler $updateHandler,
        private readonly ChangeStatusHandler $changeStatusHandler,
        private readonly GenerateApiKeyHandler $generateApiKeyHandler,
        private readonly GenerateJwtTokenHandler $generateJwtTokenHandler,
        private readonly GetApplicationManagerHandler $getHandler,
        private readonly ListApplicationManagersHandler $listHandler,
        private readonly GenericRequestMapper $requestMapper,
    ) {
    }

    #[OA\Post(
        path: '/v1/applications',
        summary: 'Create Application Manager',
        tags: ['Application Managers'],
        security: [['jwt' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'My App'),
                    new OA\Property(property: 'request_url', type: 'string', format: 'url', example: 'https://myapp.com'),
                    new OA\Property(property: 'is_active', type: 'boolean', example: true),
                    new OA\Property(property: 'ip_whitelist', type: 'array', items: new OA\Items(type: 'string', example: '127.0.0.1')),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Application Manager created'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function store(CreateApplicationManagerRequest $request): JsonResponse
    {
        /** @var CreateApplicationManagerCommandMapper $mapped */
        $mapped = $this->requestMapper->map($request, CreateApplicationManagerCommandMapper::class);
        $dto = $this->createHandler->handle($mapped->toCommand());

        return response()->json($dto->toArray(), 201);
    }

    #[OA\Get(
        path: '/v1/applications',
        summary: 'List Application Managers',
        tags: ['Application Managers'],
        security: [['jwt' => []]],
        parameters: [
            new OA\Parameter(name: 'is_active', in: 'query', description: 'Filter by active status', schema: new OA\Schema(type: 'boolean')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'List of Application Managers'),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $query = ListApplicationManagersQueryMapper::fromRequest($request);

        $result = $this->listHandler->handle($query);

        return response()->json([
            'data' => array_map(fn ($dto) => $dto->toArray(), $result),
        ]);
    }

    #[OA\Get(
        path: '/v1/applications/{id}',
        summary: 'Get Application Manager details',
        tags: ['Application Managers'],
        security: [['jwt' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Application Manager ID', schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Application Manager details'),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function show(string $id): JsonResponse
    {
        $query = new GetApplicationManagerQuery(id: Uuid::fromString($id));
        $dto = $this->getHandler->handle($query);

        return response()->json($dto->toArray());
    }

    #[OA\Put(
        path: '/v1/applications/{id}',
        summary: 'Update Application Manager',
        tags: ['Application Managers'],
        security: [['jwt' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Application Manager ID', schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'My App Updated'),
                    new OA\Property(property: 'request_url', type: 'string', format: 'url', example: 'https://myapp-updated.com'),
                    new OA\Property(property: 'is_active', type: 'boolean', example: false),
                    new OA\Property(property: 'ip_whitelist', type: 'array', items: new OA\Items(type: 'string', example: '192.168.1.1')),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Application Manager updated'),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function update(UpdateApplicationManagerRequest $request, string $id): JsonResponse
    {
        /** @var UpdateApplicationManagerCommandMapper $mapped */
        $mapped = $this->requestMapper->map($request, UpdateApplicationManagerCommandMapper::class);
        $dto = $this->updateHandler->handle($mapped->toCommand());

        return response()->json($dto->toArray());
    }

    #[OA\Patch(
        path: '/v1/applications/{id}/status',
        summary: 'Activate or deactivate Application Manager',
        tags: ['Application Managers'],
        security: [['jwt' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Application Manager ID', schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['is_active'],
                properties: [
                    new OA\Property(property: 'is_active', type: 'boolean', example: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Status updated'),
            new OA\Response(response: 404, description: 'Not found'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function changeStatus(ChangeStatusRequest $request, string $id): JsonResponse
    {
        $command = new ChangeStatusCommand(
            uuid: Uuid::fromString($id),
            isActive: $request->boolean('is_active'),
        );

        $this->changeStatusHandler->handle($command);

        return response()->json([
            'is_active' => $command->isActive,
        ]);
    }

    #[OA\Post(
        path: '/v1/applications/{id}/generate-api-key',
        summary: 'Generate API Key',
        tags: ['Application Managers'],
        security: [['jwt' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Application Manager ID', schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'API Key generated'),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function generateApiKey(string $id): JsonResponse
    {
        $command = new GenerateApiKeyCommand(id: Uuid::fromString($id));
        $dto = $this->generateApiKeyHandler->handle($command);

        return response()->json($dto->toArray());
    }

    #[OA\Post(
        path: '/v1/applications/{id}/generate-jwt-token',
        summary: 'Generate JWT Token',
        tags: ['Application Managers'],
        security: [['jwt' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Application Manager ID', schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        requestBody: new OA\RequestBody(
            required: false,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'expiration_minutes', type: 'integer', example: 60),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'JWT Token generated'),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function generateJwtToken(GenerateJwtTokenRequest $request, string $id): JsonResponse
    {
        $command = GenerateJwtTokenCommandMapper::toCommand($request, $id);
        $dto = $this->generateJwtTokenHandler->handle($command);

        return response()->json($dto->toArray());
    }
}
