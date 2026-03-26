<?php

declare(strict_types=1);

namespace App\Settings\UI\Http\Controllers\Api\V1;

use App\Settings\Application\Command\CreateChartDefinition\CreateChartDefinitionCommand;
use App\Settings\Application\Command\CreateChartDefinition\CreateChartDefinitionHandler;
use App\Settings\Application\Command\DeleteChartDefinition\DeleteChartDefinitionCommand;
use App\Settings\Application\Command\DeleteChartDefinition\DeleteChartDefinitionHandler;
use App\Settings\Application\Command\UpdateChartDefinition\UpdateChartDefinitionCommand;
use App\Settings\Application\Command\UpdateChartDefinition\UpdateChartDefinitionHandler;
use App\Settings\Application\Query\GetChartDefinition\GetChartDefinitionHandler;
use App\Settings\Application\Query\GetChartDefinition\GetChartDefinitionQuery;
use App\Settings\Application\Query\ListChartDefinitions\ListChartDefinitionsHandler;
use App\Settings\Application\Query\ListChartDefinitions\ListChartDefinitionsQuery;
use App\Settings\Domain\Exception\ChartDefinitionNotFoundException;
use App\Settings\UI\Http\Requests\V1\StoreChartDefinitionRequest;
use App\Settings\UI\Http\Requests\V1\UpdateChartDefinitionRequest;
use App\Shared\Domain\ValueObject\Uuid;
use App\Shared\UI\Http\Controllers\Api\ApiController;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

final class ChartDefinitionController extends ApiController
{
    public function __construct(
        private readonly CreateChartDefinitionHandler $createHandler,
        private readonly UpdateChartDefinitionHandler $updateHandler,
        private readonly DeleteChartDefinitionHandler $deleteHandler,
        private readonly GetChartDefinitionHandler $getHandler,
        private readonly ListChartDefinitionsHandler $listHandler,
    ) {
    }

    #[OA\Get(
        path: '/v1/settings/chart-definitions',
        summary: 'List chart definitions',
        description: 'Definitions of charts: chart type, SQL query text, and display field metadata (JSON).',
        tags: ['Settings'],
        security: [['jwt' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'OK',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', nullable: true),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'id', type: 'string', format: 'uuid'),
                                    new OA\Property(property: 'chart_type', type: 'string', example: 'line'),
                                    new OA\Property(property: 'display_fields', type: 'object'),
                                    new OA\Property(property: 'sql_query', type: 'string'),
                                    new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true),
                                    new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', nullable: true),
                                ],
                                type: 'object'
                            )
                        ),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function index(): JsonResponse
    {
        $items = $this->listHandler->handle(new ListChartDefinitionsQuery());

        return $this->success(array_map(
            static fn ($dto) => $dto->toArray(),
            $items
        ));
    }

    #[OA\Post(
        path: '/v1/settings/chart-definitions',
        summary: 'Create chart definition',
        tags: ['Settings'],
        security: [['jwt' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['chart_type', 'display_fields', 'sql_query'],
                properties: [
                    new OA\Property(property: 'chart_type', type: 'string', example: 'line'),
                    new OA\Property(property: 'display_fields', type: 'object', example: ['x' => 'date', 'y' => 'value']),
                    new OA\Property(property: 'sql_query', type: 'string', example: 'SELECT day, count(*) AS value FROM events GROUP BY day'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Created'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function store(StoreChartDefinitionRequest $request): JsonResponse
    {
        /** @var array<int|string, mixed> $displayFields */
        $displayFields = $request->validated('display_fields');

        $dto = $this->createHandler->handle(new CreateChartDefinitionCommand(
            chartType: $request->validated('chart_type'),
            displayFields: $displayFields,
            sqlQuery: $request->validated('sql_query'),
        ));

        return $this->created($dto->toArray());
    }

    #[OA\Get(
        path: '/v1/settings/chart-definitions/{id}',
        summary: 'Get chart definition by ID',
        tags: ['Settings'],
        security: [['jwt' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Chart definition UUID', schema: new OA\Schema(type: 'string', format: 'uuid')),
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
            $dto = $this->getHandler->handle(new GetChartDefinitionQuery(id: $id));

            return $this->success($dto->toArray());
        } catch (ChartDefinitionNotFoundException $e) {
            return $this->notFound($e->getMessage());
        }
    }

    #[OA\Put(
        path: '/v1/settings/chart-definitions/{id}',
        summary: 'Update chart definition',
        tags: ['Settings'],
        security: [['jwt' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Chart definition UUID', schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['chart_type', 'display_fields', 'sql_query'],
                properties: [
                    new OA\Property(property: 'chart_type', type: 'string'),
                    new OA\Property(property: 'display_fields', type: 'object'),
                    new OA\Property(property: 'sql_query', type: 'string'),
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
    public function update(UpdateChartDefinitionRequest $request, Uuid $id): JsonResponse
    {
        try {
            /** @var array<int|string, mixed> $displayFields */
            $displayFields = $request->validated('display_fields');

            $dto = $this->updateHandler->handle(new UpdateChartDefinitionCommand(
                id: $id,
                chartType: $request->validated('chart_type'),
                displayFields: $displayFields,
                sqlQuery: $request->validated('sql_query'),
            ));

            return $this->success($dto->toArray());
        } catch (ChartDefinitionNotFoundException $e) {
            return $this->notFound($e->getMessage());
        }
    }

    #[OA\Delete(
        path: '/v1/settings/chart-definitions/{id}',
        summary: 'Delete chart definition',
        tags: ['Settings'],
        security: [['jwt' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Chart definition UUID', schema: new OA\Schema(type: 'string', format: 'uuid')),
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
            $this->deleteHandler->handle(new DeleteChartDefinitionCommand(id: $id));

            return $this->noContent();
        } catch (ChartDefinitionNotFoundException $e) {
            return $this->notFound($e->getMessage());
        }
    }
}
