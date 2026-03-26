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

    public function index(): JsonResponse
    {
        $items = $this->listHandler->handle(new ListChartDefinitionsQuery());

        return $this->success(array_map(
            static fn ($dto) => $dto->toArray(),
            $items
        ));
    }

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

    public function show(Uuid $id): JsonResponse
    {
        try {
            $dto = $this->getHandler->handle(new GetChartDefinitionQuery(id: $id));

            return $this->success($dto->toArray());
        } catch (ChartDefinitionNotFoundException $e) {
            return $this->notFound($e->getMessage());
        }
    }

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
