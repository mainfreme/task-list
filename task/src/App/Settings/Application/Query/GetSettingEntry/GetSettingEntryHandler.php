<?php

declare(strict_types=1);

namespace App\Settings\Application\Query\GetSettingEntry;

use App\Settings\Application\DTO\SettingEntryDto;
use App\Settings\Application\Mapper\SettingsEntityMapper;
use App\Settings\Domain\Repository\SettingEntryRepositoryInterface;

final class GetSettingEntryHandler
{
    public function __construct(
        private readonly SettingEntryRepositoryInterface $repository,
        private readonly SettingsEntityMapper $mapper,
    ) {
    }

    public function handle(GetSettingEntryQuery $query): SettingEntryDto
    {
        $entity = $this->repository->findById($query->id);

        return $this->mapper->toSettingEntryDto($entity);
    }
}
