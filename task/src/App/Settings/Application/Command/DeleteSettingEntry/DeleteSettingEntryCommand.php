<?php

declare(strict_types=1);

namespace App\Settings\Application\Command\DeleteSettingEntry;

use App\Shared\Domain\ValueObject\Uuid;

final class DeleteSettingEntryCommand
{
    public function __construct(
        public readonly Uuid $id,
    ) {
    }
}
