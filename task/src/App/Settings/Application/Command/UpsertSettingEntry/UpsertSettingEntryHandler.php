<?php

declare(strict_types=1);

namespace App\Settings\Application\Command\UpsertSettingEntry;

use App\Settings\Application\Command\SettingsChangeDetector;
use App\Settings\Application\DTO\SettingEntryDto;
use App\Settings\Application\Event\SettingsEventDispatcherInterface;
use App\Settings\Application\Mapper\SettingsEntityMapper;
use App\Settings\Domain\Entity\SettingEntry;
use App\Settings\Domain\Event\SettingsChangedEvent;
use App\Settings\Domain\Repository\SettingEntryRepositoryInterface;
use App\Settings\Domain\ValueObject\SettingFieldType;

final class UpsertSettingEntryHandler
{
    public function __construct(
        private readonly SettingEntryRepositoryInterface $repository,
        private readonly SettingsEntityMapper $mapper,
        private readonly SettingsEventDispatcherInterface $events,
    ) {
    }

    public function handle(UpsertSettingEntryCommand $command): SettingEntryDto
    {
        $fieldType = SettingFieldType::from($command->fieldType);

        $existing = $this->repository->findByGroupAndField($command->groupKey, $command->fieldKey);
        if ($existing !== null) {
            $before = $this->mapper->toSettingEntryDto($existing)->toArray();
            $existing->update($fieldType, $command->value);
            $this->repository->save($existing);
            $dto = $this->mapper->toSettingEntryDto(
                $this->repository->findById($existing->getId())
            );
            $after = $dto->toArray();

            $this->events->dispatch(new SettingsChangedEvent(
                resourceType: 'setting_entry',
                resourceId: $existing->getId()->getValue(),
                operation: SettingsChangedEvent::OPERATION_UPDATED,
                before: $before,
                after: $after,
                changedFields: SettingsChangeDetector::changedFields($before, $after),
                actorId: $command->context?->actorId,
                requestUrl: $command->context?->requestUrl,
                ipAddress: $command->context?->ipAddress,
                userAgent: $command->context?->userAgent,
            ));

            return $dto;
        }

        $entry = SettingEntry::create(
            $command->groupKey,
            $command->fieldKey,
            $fieldType,
            $command->value,
        );
        $this->repository->save($entry);
        $dto = $this->mapper->toSettingEntryDto(
            $this->repository->findById($entry->getId())
        );
        $after = $dto->toArray();

        $this->events->dispatch(new SettingsChangedEvent(
            resourceType: 'setting_entry',
            resourceId: $entry->getId()->getValue(),
            operation: SettingsChangedEvent::OPERATION_CREATED,
            before: null,
            after: $after,
            changedFields: SettingsChangeDetector::changedFields(null, $after),
            actorId: $command->context?->actorId,
            requestUrl: $command->context?->requestUrl,
            ipAddress: $command->context?->ipAddress,
            userAgent: $command->context?->userAgent,
        ));

        return $dto;
    }
}
