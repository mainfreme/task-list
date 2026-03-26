<?php

declare(strict_types=1);

namespace App\Settings\Application\Command\DeleteIntegrationAccount;

use App\Shared\Domain\ValueObject\Uuid;

final class DeleteIntegrationAccountCommand
{
    public function __construct(
        public readonly Uuid $id,
    ) {
    }
}
