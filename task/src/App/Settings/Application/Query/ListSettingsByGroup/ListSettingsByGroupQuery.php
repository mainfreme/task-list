<?php

declare(strict_types=1);

namespace App\Settings\Application\Query\ListSettingsByGroup;

final class ListSettingsByGroupQuery
{
    public function __construct(
        public readonly string $groupKey,
    ) {
    }
}
