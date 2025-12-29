<?php

declare(strict_types=1);

namespace App\Domain\ApplicationManager\Exception;

use Exception;

final class ApplicationManagerNotFoundException extends Exception
{
    public static function byId(int $id): self
    {
        return new self("ApplicationManager with ID {$id} not found");
    }

    public static function byApiKey(string $apiKey): self
    {
        return new self("ApplicationManager with API Key not found");
    }
}

