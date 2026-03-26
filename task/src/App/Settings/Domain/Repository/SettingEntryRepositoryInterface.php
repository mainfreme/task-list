<?php

declare(strict_types=1);

namespace App\Settings\Domain\Repository;

use App\Settings\Domain\Entity\SettingEntry;
use App\Shared\Domain\ValueObject\Uuid;

interface SettingEntryRepositoryInterface
{
    public function findById(Uuid $id): SettingEntry;

    public function findByGroupAndField(string $groupKey, string $fieldKey): ?SettingEntry;

    /**
     * @return SettingEntry[]
     */
    public function findByGroup(string $groupKey): array;

    /**
     * @return array<string, SettingEntry[]> group_key => entries
     */
    public function findAllGroupedByGroupKey(): array;

    public function save(SettingEntry $entry): void;

    public function delete(SettingEntry $entry): void;
}
