<?php

declare(strict_types=1);

namespace App\Task\Application\Command\DeleteTask;

final class DeleteTaskCommand
{
    public function __construct(public readonly int $id) {}
}
