<?php

declare(strict_types=1);

namespace App\Settings\UI\Http\Controllers\Api\V1;

use App\Settings\Application\Command\DeleteSettingEntry\DeleteSettingEntryCommand;
use App\Settings\Application\Command\DeleteSettingEntry\DeleteSettingEntryHandler;
use App\Settings\Application\Command\SettingsCommandContext;
use App\Settings\Application\Command\UpsertSettingEntry\UpsertSettingEntryCommand;
use App\Settings\Application\Command\UpsertSettingEntry\UpsertSettingEntryHandler;
use App\Settings\Application\Query\GetAllSettingsGrouped\GetAllSettingsGroupedHandler;
use App\Settings\Application\Query\GetAllSettingsGrouped\GetAllSettingsGroupedQuery;
use App\Settings\Application\Query\GetSettingEntry\GetSettingEntryHandler;
use App\Settings\Application\Query\GetSettingEntry\GetSettingEntryQuery;
use App\Settings\Application\Query\ListSettingsByGroup\ListSettingsByGroupHandler;
use App\Settings\Application\Query\ListSettingsByGroup\ListSettingsByGroupQuery;
use App\Settings\Domain\Exception\SettingEntryNotFoundException;
use App\Settings\UI\Http\Requests\V1\UpsertSettingEntryRequest;
use App\Shared\Domain\ValueObject\Uuid;
use App\Shared\UI\Http\Controllers\Api\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

final class SettingEntryController extends ApiController
{
    public function __construct(
        private readonly UpsertSettingEntryHandler $upsertHandler,
        private readonly DeleteSettingEntryHandler $deleteHandler,
        private readonly GetSettingEntryHandler $getHandler,
        private readonly ListSettingsByGroupHandler $listByGroupHandler,
        private readonly GetAllSettingsGroupedHandler $getAllGroupedHandler,
    ) {
    }

    #[OA\Get(
        path: '/v1/settings/entries/grouped',
        summary: 'Get all setting entries grouped by group_key',
        description: 'Returns an object keyed by group_key; each value is an array of entries (group_key, field_key, field_type, value, …).',
        tags: ['Settings'],
        security: [['jwt' => []]],
        responses: [
            new OA\Response(response: 200, description: 'OK'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function grouped(): JsonResponse
    {
        $data = $this->getAllGroupedHandler->handle(new GetAllSettingsGroupedQuery());

        return $this->success($data);
    }

    #[OA\Get(
        path: '/v1/settings/groups/{groupKey}',
        summary: 'List setting entries for one group',
        tags: ['Settings'],
        security: [['jwt' => []]],
        parameters: [
            new OA\Parameter(
                name: 'groupKey',
                in: 'path',
                required: true,
                description: 'Group key (e.g. dashboard, notifications)',
                schema: new OA\Schema(type: 'string', example: 'dashboard')
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'OK'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function indexByGroup(string $groupKey): JsonResponse
    {
        $items = $this->listByGroupHandler->handle(new ListSettingsByGroupQuery(groupKey: $groupKey));

        return $this->success(array_map(
            static fn ($dto) => $dto->toArray(),
            $items
        ));
    }

    #[OA\Get(
        path: '/v1/settings/entries/{id}',
        summary: 'Get setting entry by ID',
        tags: ['Settings'],
        security: [['jwt' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Setting entry UUID', schema: new OA\Schema(type: 'string', format: 'uuid')),
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
            $dto = $this->getHandler->handle(new GetSettingEntryQuery(id: $id));

            return $this->success($dto->toArray());
        } catch (SettingEntryNotFoundException $e) {
            return $this->notFound($e->getMessage());
        }
    }

    #[OA\Put(
        path: '/v1/settings/entries',
        summary: 'Upsert setting entry',
        description: 'Creates or updates a row by unique (group_key, field_key).',
        tags: ['Settings'],
        security: [['jwt' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['group_key', 'field_key', 'field_type'],
                properties: [
                    new OA\Property(property: 'group_key', type: 'string', example: 'dashboard'),
                    new OA\Property(property: 'field_key', type: 'string', example: 'theme'),
                    new OA\Property(property: 'field_type', type: 'string', enum: ['string', 'int', 'bool', 'json']),
                    new OA\Property(property: 'value', type: 'string', nullable: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'OK'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function upsert(UpsertSettingEntryRequest $request): JsonResponse
    {
        $dto = $this->upsertHandler->handle(new UpsertSettingEntryCommand(
            groupKey: $request->validated('group_key'),
            fieldKey: $request->validated('field_key'),
            fieldType: $request->validated('field_type'),
            value: $request->validated('value'),
            context: $this->buildContext($request),
        ));

        return $this->success($dto->toArray());
    }

    #[OA\Delete(
        path: '/v1/settings/entries/{id}',
        summary: 'Delete setting entry',
        tags: ['Settings'],
        security: [['jwt' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Setting entry UUID', schema: new OA\Schema(type: 'string', format: 'uuid')),
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

            $this->deleteHandler->handle(new DeleteSettingEntryCommand(
                id: $id,
                context: $this->buildContext($request),
            ));

            return $this->noContent();
        } catch (SettingEntryNotFoundException $e) {
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
