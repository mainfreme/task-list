<?php

declare(strict_types=1);

namespace App\Settings\Application\Query\ListSettingsByGroup;

use App\Settings\Application\DTO\SettingEntryDto;
use App\Settings\Application\Mapper\SettingsEntityMapper;
use App\Settings\Domain\Repository\SettingEntryRepositoryInterface;

final class ListSettingsByGroupHandler
{
    public function __construct(
        private readonly SettingEntryRepositoryInterface $repository,
        private readonly SettingsEntityMapper $mapper,
    ) {
    }

    /**
     * @return SettingEntryDto[]
     */
    public function handle(ListSettingsByGroupQuery $query): array
    {
        return array_map(
            fn ($entity) => $this->mapper->toSettingEntryDto($entity),
            $this->repository->findByGroup($query->groupKey)
        );
    }
}
