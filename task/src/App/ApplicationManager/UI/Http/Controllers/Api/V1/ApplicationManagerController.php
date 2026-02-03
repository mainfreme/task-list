<?php

declare(strict_types=1);

namespace App\ApplicationManager\UI\Http\Controllers\Api\V1;

use App\ApplicationManager\Application\Command\CreateApplicationManager\CreateApplicationManagerCommand;
use App\ApplicationManager\Application\Command\CreateApplicationManager\CreateApplicationManagerHandler;
use App\ApplicationManager\Application\Command\GenerateApiKey\GenerateApiKeyCommand;
use App\ApplicationManager\Application\Command\GenerateApiKey\GenerateApiKeyHandler;
use App\ApplicationManager\Application\Command\GenerateJwtToken\GenerateJwtTokenCommand;
use App\ApplicationManager\Application\Command\GenerateJwtToken\GenerateJwtTokenHandler;
use App\ApplicationManager\Application\Command\UpdateApplicationManager\UpdateApplicationManagerCommand;
use App\ApplicationManager\Application\Command\UpdateApplicationManager\UpdateApplicationManagerHandler;
use App\ApplicationManager\Application\Query\GetApplicationManager\GetApplicationManagerHandler;
use App\ApplicationManager\Application\Query\GetApplicationManager\GetApplicationManagerQuery;
use App\ApplicationManager\Application\Query\ListApplicationManagers\ListApplicationManagersHandler;
use App\ApplicationManager\Application\Query\ListApplicationManagers\ListApplicationManagersQuery;
use App\ApplicationManager\Domain\ValueObject\Uuid;
use App\ApplicationManager\Domain\ValueObject\ApplicationName;
use App\ApplicationManager\Domain\ValueObject\IpWhitelist;
use App\ApplicationManager\Domain\ValueObject\RequestUrl;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

final class ApplicationManagerController
{
    public function __construct(
        private readonly CreateApplicationManagerHandler $createHandler,
        private readonly UpdateApplicationManagerHandler $updateHandler,
        private readonly GenerateApiKeyHandler $generateApiKeyHandler,
        private readonly GenerateJwtTokenHandler $generateJwtTokenHandler,
        private readonly GetApplicationManagerHandler $getHandler,
        private readonly ListApplicationManagersHandler $listHandler,
    ) {
    }

    #[OA\Post(
        path: "/v1/applications",
        summary: "Create Application Manager",
        tags: ["Application Managers"],
        security: [["jwt" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "My App"),
                    new OA\Property(property: "request_url", type: "string", format: "url", example: "https://myapp.com"),
                    new OA\Property(property: "is_active", type: "boolean", example: true),
                    new OA\Property(property: "ip_whitelist", type: "array", items: new OA\Items(type: "string", example: "127.0.0.1"))
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Application Manager created"),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'request_url' => 'nullable|string|url|max:255',
            'is_active' => 'sometimes|boolean',
            'ip_whitelist' => 'nullable|array',
            'ip_whitelist.*' => 'ip',
        ]);

        $command = new CreateApplicationManagerCommand(
            name: ApplicationName::fromString($validated['name']),
            requestUrl: RequestUrl::fromNullable($validated['request_url'] ?? null),
            isActive: $validated['is_active'] ?? true,
            ipWhitelist: IpWhitelist::fromNullable($validated['ip_whitelist'] ?? null),
        );

        $dto = $this->createHandler->handle($command);

        return response()->json($dto->toArray(), 201);
    }

    #[OA\Get(
        path: "/v1/applications",
        summary: "List Application Managers",
        tags: ["Application Managers"],
        security: [["jwt" => []]],
        parameters: [
            new OA\Parameter(name: "is_active", in: "query", description: "Filter by active status", schema: new OA\Schema(type: "boolean"))
        ],
        responses: [
            new OA\Response(response: 200, description: "List of Application Managers")
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $query = new ListApplicationManagersQuery(
            isActive: $request->has('is_active') ? (bool) $request->input('is_active') : null,
        );

        $result = $this->listHandler->handle($query);

        return response()->json([
            'data' => array_map(fn ($dto) => $dto->toArray(), $result),
        ]);
    }

    #[OA\Get(
        path: "/v1/applications/{id}",
        summary: "Get Application Manager details",
        tags: ["Application Managers"],
        security: [["jwt" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "Application Manager ID", schema: new OA\Schema(type: "string", format: "uuid"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Application Manager details"),
            new OA\Response(response: 404, description: "Not found")
        ]
    )]
    public function show(string $id): JsonResponse
    {
        $query = new GetApplicationManagerQuery(id: Uuid::fromString($id));
        $dto = $this->getHandler->handle($query);

        return response()->json($dto->toArray());
    }

    #[OA\Put(
        path: "/v1/applications/{id}",
        summary: "Update Application Manager",
        tags: ["Application Managers"],
        security: [["jwt" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "Application Manager ID", schema: new OA\Schema(type: "string", format: "uuid"))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "name", type: "string", example: "My App Updated"),
                    new OA\Property(property: "request_url", type: "string", format: "url", example: "https://myapp-updated.com"),
                    new OA\Property(property: "is_active", type: "boolean", example: false),
                    new OA\Property(property: "ip_whitelist", type: "array", items: new OA\Items(type: "string", example: "192.168.1.1"))
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Application Manager updated"),
            new OA\Response(response: 404, description: "Not found")
        ]
    )]
    public function update(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'request_url' => 'nullable|string|url|max:255',
            'is_active' => 'sometimes|boolean',
            'ip_whitelist' => 'nullable|array',
            'ip_whitelist.*' => 'ip',
        ]);

        $command = new UpdateApplicationManagerCommand(
            id: Uuid::fromString($id),
            name: array_key_exists('name', $validated)
                ? ApplicationName::fromString($validated['name'])
                : null,
            requestUrl: array_key_exists('request_url', $validated)
                ? RequestUrl::fromNullable($validated['request_url'])
                : null,
            isActive: $validated['is_active'] ?? null,
            ipWhitelist: array_key_exists('ip_whitelist', $validated)
                ? IpWhitelist::fromNullable($validated['ip_whitelist'])
                : null,
        );

        $dto = $this->updateHandler->handle($command);

        return response()->json($dto->toArray());
    }

    #[OA\Post(
        path: "/v1/applications/{id}/generate-api-key",
        summary: "Generate API Key",
        tags: ["Application Managers"],
        security: [["jwt" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "Application Manager ID", schema: new OA\Schema(type: "string", format: "uuid"))
        ],
        responses: [
            new OA\Response(response: 200, description: "API Key generated"),
            new OA\Response(response: 404, description: "Not found")
        ]
    )]
    public function generateApiKey(string $id): JsonResponse
    {
        $command = new GenerateApiKeyCommand(id: Uuid::fromString($id));
        $dto = $this->generateApiKeyHandler->handle($command);

        return response()->json($dto->toArray());
    }

    #[OA\Post(
        path: "/v1/applications/{id}/generate-jwt-token",
        summary: "Generate JWT Token",
        tags: ["Application Managers"],
        security: [["jwt" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "Application Manager ID", schema: new OA\Schema(type: "string", format: "uuid"))
        ],
        requestBody: new OA\RequestBody(
            required: false,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "expiration_minutes", type: "integer", example: 60)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "JWT Token generated"),
            new OA\Response(response: 404, description: "Not found")
        ]
    )]
    public function generateJwtToken(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'expiration_minutes' => 'nullable|integer|min:1|max:525600', // Max 1 year
        ]);

        $command = new GenerateJwtTokenCommand(
            uuid: Uuid::fromString($id),
            expirationMinutes: $validated['expiration_minutes'] ?? null,
        );

        $token = $this->generateJwtTokenHandler->handle($command);
        $expirationMinutes = $validated['expiration_minutes'] ?? (int) env('JWT_EXPIRATION_MINUTES', 60 * 24);

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => $expirationMinutes,
        ]);
    }
}
