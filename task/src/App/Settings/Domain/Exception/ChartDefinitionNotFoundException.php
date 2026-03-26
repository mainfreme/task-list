<?php

declare(strict_types=1);

namespace App\Settings\Domain\Exception;

use RuntimeException;

final class ChartDefinitionNotFoundException extends RuntimeException
{
    public static function byId(string $id): self
    {
        return new self(sprintf('Chart definition not found: %s', $id));
    }
}
