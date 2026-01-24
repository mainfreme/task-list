<?php

declare(strict_types=1);

namespace App\Task\Domain\Exception;

use Exception;

final class TaskNotFoundException extends Exception
{
    public static function byId(string $id): self
    {
        return new self("Task with ID {$id} not found");
    }
}
