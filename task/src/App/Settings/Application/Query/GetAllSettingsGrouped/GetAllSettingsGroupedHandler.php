<?php

declare(strict_types=1);

namespace App\Settings\Application\Query\GetAllSettingsGrouped;

use App\Settings\Application\Mapper\SettingsEntityMapper;
use App\Settings\Domain\Repository\SettingEntryRepositoryInterface;

final class GetAllSettingsGroupedHandler
{
    public function __construct(
        private readonly SettingEntryRepositoryInterface $repository,
        private readonly SettingsEntityMapper $mapper,
    ) {
    }

    /**
     * @return array<string, array<int, array<string, mixed>>>
     */
    public function handle(GetAllSettingsGroupedQuery $query): array
    {
        $grouped = $this->repository->findAllGroupedByGroupKey();
        $out = [];
        foreach ($grouped as $groupKey => $entries) {
            $out[$groupKey] = array_map(
                fn ($entity) => $this->mapper->toSettingEntryDto($entity)->toArray(),
                $entries
            );
        }

        return $out;
    }
}
