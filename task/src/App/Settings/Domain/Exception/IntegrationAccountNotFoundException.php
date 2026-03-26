<?php

declare(strict_types=1);

namespace App\Settings\Domain\Exception;

use RuntimeException;

final class IntegrationAccountNotFoundException extends RuntimeException
{
    public static function byId(string $id): self
    {
        return new self(sprintf('Integration account not found: %s', $id));
    }
}
