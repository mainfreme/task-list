<?php

declare(strict_types=1);

namespace App\Auth\Domain\Exception;

use App\Shared\Domain\ValueObject\Uuid;

final class UserNotFoundException extends \DomainException
{
    public static function byId(Uuid $id): self
    {
        return new self(
            sprintf('User with ID "%s" not found.', $id->getValue())
        );
    }
}
