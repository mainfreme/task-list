<?php

declare(strict_types=1);

namespace App\Crm\Application\Command\DeleteClient;

use App\Shared\Domain\ValueObject\Uuid;

final class DeleteClientCommand
{
    public function __construct(
        public readonly Uuid $id,
    ) {
    }
}
