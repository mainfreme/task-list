<?php

declare(strict_types=1);

namespace App\Settings\Application\Command\UpsertSettingEntry;

use App\Settings\Application\DTO\SettingEntryDto;
use App\Settings\Application\Mapper\SettingsEntityMapper;
use App\Settings\Domain\Entity\SettingEntry;
use App\Settings\Domain\Repository\SettingEntryRepositoryInterface;
use App\Settings\Domain\ValueObject\SettingFieldType;

final class UpsertSettingEntryHandler
{
    public function __construct(
        private readonly SettingEntryRepositoryInterface $repository,
        private readonly SettingsEntityMapper $mapper,
    ) {
    }

    public function handle(UpsertSettingEntryCommand $command): SettingEntryDto
    {
        $fieldType = SettingFieldType::from($command->fieldType);

        $existing = $this->repository->findByGroupAndField($command->groupKey, $command->fieldKey);
        if ($existing !== null) {
            $existing->update($fieldType, $command->value);
            $this->repository->save($existing);

            return $this->mapper->toSettingEntryDto(
                $this->repository->findById($existing->getId())
            );
        }

        $entry = SettingEntry::create(
            $command->groupKey,
            $command->fieldKey,
            $fieldType,
            $command->value,
        );
        $this->repository->save($entry);

        return $this->mapper->toSettingEntryDto(
            $this->repository->findById($entry->getId())
        );
    }
}
