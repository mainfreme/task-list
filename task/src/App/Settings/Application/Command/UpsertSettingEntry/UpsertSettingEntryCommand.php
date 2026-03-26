<?php

declare(strict_types=1);

namespace App\Settings\Application\Command\UpsertSettingEntry;

final class UpsertSettingEntryCommand
{
    public function __construct(
        public readonly string $groupKey,
        public readonly string $fieldKey,
        public readonly string $fieldType,
        public readonly ?string $value,
    ) {
    }
}
