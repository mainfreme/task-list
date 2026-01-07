<?php

declare(strict_types=1);

namespace App\Application\Task\Command\DeleteTask;

final class DeleteTaskCommand
{
    public function __construct(public readonly int $id) {}
}