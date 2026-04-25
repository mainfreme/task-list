<?php

declare(strict_types=1);

namespace App\Settings\Application\Command;

final class SettingsChangeDetector
{
    /**
     * @param array<string, mixed>|null $before
     * @param array<string, mixed>|null $after
     * @return array<int, string>
     */
    public static function changedFields(?array $before, ?array $after): array
    {
        $beforeSafe = $before ?? [];
        $afterSafe = $after ?? [];

        /** @var array<int, string> $keys */
        $keys = array_values(array_unique(array_merge(array_keys($beforeSafe), array_keys($afterSafe))));

        $changed = [];
        foreach ($keys as $key) {
            if (($beforeSafe[$key] ?? null) !== ($afterSafe[$key] ?? null)) {
                $changed[] = $key;
            }
        }

        return $changed;
    }
}
