<?php

declare(strict_types=1);

namespace App\Settings\Application\Query\GetSettingEntry;

use App\Shared\Domain\ValueObject\Uuid;

final class GetSettingEntryQuery
{
    public function __construct(
        public readonly Uuid $id,
    ) {
    }
}
