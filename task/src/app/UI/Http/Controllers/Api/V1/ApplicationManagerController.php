<?php

declare(strict_types=1);

namespace App\UI\Http\Controllers\Api\V1;

use Application\ApplicationManager\Command\CreateApplicationManager\CreateApplicationManagerCommand;
use Application\ApplicationManager\Command\CreateApplicationManager\CreateApplicationManagerHandler;
use Application\ApplicationManager\Command\UpdateApplicationManager\UpdateApplicationManagerCommand;
use Application\ApplicationManager\Command\UpdateApplicationManager\UpdateApplicationManagerHandler;
use Application\ApplicationManager\Command\GenerateApiKey\GenerateApiKeyCommand;
use Application\ApplicationManager\Command\GenerateApiKey\GenerateApiKeyHandler;
use Application\ApplicationManager\Query\GetApplicationManager\GetApplicationManagerQuery;
use Application\ApplicationManager\Query\GetApplicationManager\GetApplicationManagerHandler;
use Application\ApplicationManager\Query\ListApplicationManagers\ListApplicationManagersQuery;
use Application\ApplicationManager\Query\ListApplicationManagers\ListApplicationManagersHandler;
use App\UI\Http\Requests\Api\V1\StoreApplicationManagerRequest;
use App\UI\Http\Requests\Api\V1\UpdateApplicationManagerRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ApplicationManagerController
{
    public function __construct(
        private readonly CreateApplicationManagerHandler $createHandler,
        private readonly UpdateApplicationManagerHandler $updateHandler,
        private readonly GenerateApiKeyHandler $generateApiKeyHandler,
        private readonly GetApplicationManagerHandler $getHandler,
        private readonly ListApplicationManagersHandler $listHandler,
    ) {
    }

    public function store(StoreApplicationManagerRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $command = new CreateApplicationManagerCommand(
            name: $validated['name'],
            requestUrl: $validated['request_url'] ?? null,
            isActive: $validated['is_active'] ?? true,
            ipWhitelist: $validated['ip_whitelist'] ?? null,
        );

        $dto = $this->createHandler->handle($command);

        return response()->json($dto->toArray(), 201);
    }

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

    public function show(int $id): JsonResponse
    {
        $query = new GetApplicationManagerQuery(id: $id);
        $dto = $this->getHandler->handle($query);

        return response()->json($dto->toArray());
    }

    public function update(UpdateApplicationManagerRequest $request, int $id): JsonResponse
    {
        $validated = $request->validated();

        $command = new UpdateApplicationManagerCommand(
            id: $id,
            name: $validated['name'] ?? null,
            requestUrl: $validated['request_url'] ?? null,
            isActive: $validated['is_active'] ?? null,
            ipWhitelist: $validated['ip_whitelist'] ?? null,
        );

        $dto = $this->updateHandler->handle($command);

        return response()->json($dto->toArray());
    }

    public function generateApiKey(int $id): JsonResponse
    {
        $command = new GenerateApiKeyCommand(id: $id);
        $dto = $this->generateApiKeyHandler->handle($command);

        return response()->json($dto->toArray());
    }
}

