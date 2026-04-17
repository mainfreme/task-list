<?php

declare(strict_types=1);

namespace App\Settings\UI\Http\Controllers\Api\V1;

use App\Settings\Application\Command\CreateIntegrationAccount\CreateIntegrationAccountCommand;
use App\Settings\Application\Command\CreateIntegrationAccount\CreateIntegrationAccountHandler;
use App\Settings\Application\Command\DeleteIntegrationAccount\DeleteIntegrationAccountCommand;
use App\Settings\Application\Command\DeleteIntegrationAccount\DeleteIntegrationAccountHandler;
use App\Settings\Application\Command\SettingsCommandContext;
use App\Settings\Application\Command\SetIntegrationAccountEnabled\SetIntegrationAccountEnabledCommand;
use App\Settings\Application\Command\SetIntegrationAccountEnabled\SetIntegrationAccountEnabledHandler;
use App\Settings\Application\Command\UpdateIntegrationAccount\UpdateIntegrationAccountCommand;
use App\Settings\Application\Command\UpdateIntegrationAccount\UpdateIntegrationAccountHandler;
use App\Settings\Application\Query\GetIntegrationAccount\GetIntegrationAccountHandler;
use App\Settings\Application\Query\GetIntegrationAccount\GetIntegrationAccountQuery;
use App\Settings\Application\Query\ListIntegrationAccounts\ListIntegrationAccountsHandler;
use App\Settings\Application\Query\ListIntegrationAccounts\ListIntegrationAccountsQuery;
use App\Settings\Domain\Exception\IntegrationAccountNotFoundException;
use App\Settings\UI\Http\Requests\V1\SetIntegrationAccountEnabledRequest;
use App\Settings\UI\Http\Requests\V1\StoreIntegrationAccountRequest;
use App\Settings\UI\Http\Requests\V1\UpdateIntegrationAccountRequest;
use App\Shared\Domain\ValueObject\Uuid;
use App\Shared\UI\Http\Controllers\Api\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

final class IntegrationAccountController extends ApiController
{
    public function __construct(
        private readonly CreateIntegrationAccountHandler $createHandler,
        private readonly UpdateIntegrationAccountHandler $updateHandler,
        private readonly DeleteIntegrationAccountHandler $deleteHandler,
        private readonly SetIntegrationAccountEnabledHandler $setEnabledHandler,
        private readonly GetIntegrationAccountHandler $getHandler,
        private readonly ListIntegrationAccountsHandler $listHandler,
    ) {
    }

    #[OA\Get(
        path: '/v1/settings/integration-accounts',
        summary: 'List integration accounts',
        description: 'Social / API integrations. List view returns masked credentials (tokens/secrets).',
        tags: ['Settings'],
        security: [['jwt' => []]],
        responses: [
            new OA\Response(response: 200, description: 'OK'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function index(): JsonResponse
    {
        $items = $this->listHandler->handle(new ListIntegrationAccountsQuery());

        return $this->success(array_map(
            static fn ($dto) => $dto->toArray(),
            $items
        ));
    }

    #[OA\Post(
        path: '/v1/settings/integration-accounts',
        summary: 'Create integration account',
        description: 'Stores name, enabled flag, external account id, provider, and credentials as JSON.',
        tags: ['Settings'],
        security: [['jwt' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'credentials'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Fanpage – Usługi'),
                    new OA\Property(property: 'enabled', type: 'boolean', example: true),
                    new OA\Property(property: 'external_account_id', type: 'string', nullable: true, example: '123456789012345'),
                    new OA\Property(property: 'provider', type: 'string', nullable: true, example: 'facebook'),
                    new OA\Property(
                        property: 'credentials',
                        type: 'object',
                        example: ['accessToken' => '…', 'pageId' => '…', 'appId' => '…']
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Created'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function store(StoreIntegrationAccountRequest $request): JsonResponse
    {
        /** @var array<string, mixed> $credentials */
        $credentials = $request->validated('credentials');

        $dto = $this->createHandler->handle(new CreateIntegrationAccountCommand(
            name: $request->validated('name'),
            enabled: (bool) $request->validated('enabled', true),
            externalAccountId: $request->validated('external_account_id'),
            provider: $request->validated('provider'),
            credentials: $credentials,
            context: $this->buildContext($request),
        ));

        return $this->created($dto->toArray());
    }

    #[OA\Get(
        path: '/v1/settings/integration-accounts/{id}',
        summary: 'Get integration account by ID',
        description: 'Returns full credentials (decrypted server-side).',
        tags: ['Settings'],
        security: [['jwt' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Integration account UUID', schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'OK'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function show(Uuid $id): JsonResponse
    {
        try {
            $dto = $this->getHandler->handle(new GetIntegrationAccountQuery(id: $id));

            return $this->success($dto->toArray());
        } catch (IntegrationAccountNotFoundException $e) {
            return $this->notFound($e->getMessage());
        }
    }

    #[OA\Put(
        path: '/v1/settings/integration-accounts/{id}',
        summary: 'Update integration account',
        tags: ['Settings'],
        security: [['jwt' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Integration account UUID', schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'enabled', 'credentials'],
                properties: [
                    new OA\Property(property: 'name', type: 'string'),
                    new OA\Property(property: 'enabled', type: 'boolean'),
                    new OA\Property(property: 'external_account_id', type: 'string', nullable: true),
                    new OA\Property(property: 'provider', type: 'string', nullable: true),
                    new OA\Property(property: 'credentials', type: 'object'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'OK'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 404, description: 'Not found'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function update(UpdateIntegrationAccountRequest $request, Uuid $id): JsonResponse
    {
        try {
            /** @var array<string, mixed> $credentials */
            $credentials = $request->validated('credentials');

            $dto = $this->updateHandler->handle(new UpdateIntegrationAccountCommand(
                id: $id,
                name: $request->validated('name'),
                enabled: (bool) $request->validated('enabled'),
                externalAccountId: $request->validated('external_account_id'),
                provider: $request->validated('provider'),
                credentials: $credentials,
                context: $this->buildContext($request),
            ));

            return $this->success($dto->toArray());
        } catch (IntegrationAccountNotFoundException $e) {
            return $this->notFound($e->getMessage());
        }
    }

    #[OA\Delete(
        path: '/v1/settings/integration-accounts/{id}',
        summary: 'Soft-delete integration account',
        tags: ['Settings'],
        security: [['jwt' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Integration account UUID', schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'No content'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function destroy(Uuid $id): JsonResponse
    {
        try {
            /** @var Request $request */
            $request = request();

            $this->deleteHandler->handle(new DeleteIntegrationAccountCommand(
                id: $id,
                context: $this->buildContext($request),
            ));

            return $this->noContent();
        } catch (IntegrationAccountNotFoundException $e) {
            return $this->notFound($e->getMessage());
        }
    }

    #[OA\Patch(
        path: '/v1/settings/integration-accounts/{id}/enabled',
        summary: 'Enable or disable integration account',
        tags: ['Settings'],
        security: [['jwt' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Integration account UUID', schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['enabled'],
                properties: [
                    new OA\Property(property: 'enabled', type: 'boolean', example: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'OK'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 404, description: 'Not found'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function patchEnabled(SetIntegrationAccountEnabledRequest $request, Uuid $id): JsonResponse
    {
        try {
            $dto = $this->setEnabledHandler->handle(new SetIntegrationAccountEnabledCommand(
                id: $id,
                enabled: (bool) $request->validated('enabled'),
                context: $this->buildContext($request),
            ));

            return $this->success($dto->toArray());
        } catch (IntegrationAccountNotFoundException $e) {
            return $this->notFound($e->getMessage());
        }
    }

    private function buildContext(Request $request): SettingsCommandContext
    {
        $actorId = $request->attributes->get('user_id');

        return new SettingsCommandContext(
            actorId: $actorId !== null ? $actorId->getValue() : null,
            requestUrl: $request->fullUrl(),
            ipAddress: $request->ip(),
            userAgent: $request->userAgent(),
        );
    }
}
