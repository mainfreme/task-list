<?php

declare(strict_types=1);

namespace App\Task\Application\Command\DeleteTask;

use App\Shared\Domain\ValueObject\Uuid;

final class DeleteTaskCommand
{
    public function __construct(public readonly Uuid $id)
    {
    }
}
