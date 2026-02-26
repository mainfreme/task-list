<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

use InvalidArgumentException;

final class Uuid extends AbstractUuid
{
    protected function validate(): void
    {
        if (!preg_match('/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[1-7][0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}$/', $this->getValue())) {
            throw new InvalidArgumentException('Application ID must be a valid UUID');
        }
    }
}
