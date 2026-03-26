<?php

declare(strict_types=1);

namespace App\Settings\UI\Http\Controllers\Api\V1;

use App\Settings\Application\Command\DeleteSettingEntry\DeleteSettingEntryCommand;
use App\Settings\Application\Command\DeleteSettingEntry\DeleteSettingEntryHandler;
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

    public function grouped(): JsonResponse
    {
        $data = $this->getAllGroupedHandler->handle(new GetAllSettingsGroupedQuery());

        return $this->success($data);
    }

    public function indexByGroup(string $groupKey): JsonResponse
    {
        $items = $this->listByGroupHandler->handle(new ListSettingsByGroupQuery(groupKey: $groupKey));

        return $this->success(array_map(
            static fn ($dto) => $dto->toArray(),
            $items
        ));
    }

    public function show(Uuid $id): JsonResponse
    {
        try {
            $dto = $this->getHandler->handle(new GetSettingEntryQuery(id: $id));

            return $this->success($dto->toArray());
        } catch (SettingEntryNotFoundException $e) {
            return $this->notFound($e->getMessage());
        }
    }

    public function upsert(UpsertSettingEntryRequest $request): JsonResponse
    {
        $dto = $this->upsertHandler->handle(new UpsertSettingEntryCommand(
            groupKey: $request->validated('group_key'),
            fieldKey: $request->validated('field_key'),
            fieldType: $request->validated('field_type'),
            value: $request->validated('value'),
        ));

        return $this->success($dto->toArray());
    }

    public function destroy(Uuid $id): JsonResponse
    {
        try {
            $this->deleteHandler->handle(new DeleteSettingEntryCommand(id: $id));

            return $this->noContent();
        } catch (SettingEntryNotFoundException $e) {
            return $this->notFound($e->getMessage());
        }
    }
}
