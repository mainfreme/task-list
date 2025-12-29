<?php

declare(strict_types=1);

namespace App\Domain\Task\Exception;

use Exception;

final class TaskNotFoundException extends Exception
{
    public static function byId(int $id): self
    {
        return new self("Task with ID {$id} not found");
    }
}

