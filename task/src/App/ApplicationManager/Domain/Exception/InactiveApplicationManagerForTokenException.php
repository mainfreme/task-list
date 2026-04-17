<?php

declare(strict_types=1);

namespace App\ApplicationManager\Domain\Exception;

use Exception;

final class InactiveApplicationManagerForTokenException extends Exception
{
    public static function create(): self
    {
        return new self('Cannot generate JWT token for inactive application');
    }
}
