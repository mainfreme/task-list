<?php

declare(strict_types=1);

namespace App\Settings\Domain\Exception;

use RuntimeException;

final class SettingEntryNotFoundException extends RuntimeException
{
    public static function byId(string $id): self
    {
        return new self(sprintf('Setting entry not found: %s', $id));
    }

    public static function byGroupAndField(string $groupKey, string $fieldKey): self
    {
        return new self(sprintf('Setting entry not found: %s.%s', $groupKey, $fieldKey));
    }
}
