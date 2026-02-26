<?php

declare(strict_types=1);

namespace App\ApplicationManager\Application\Command\UpdateApplicationManager;

use App\Shared\Domain\ValueObject\Uuid;

final class ChangeStatusCommand
{
    public function __construct(
        public readonly Uuid $uuid,
        public readonly bool $isActive,
    ) {
    }
}
