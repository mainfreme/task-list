<?php

declare(strict_types=1);

namespace App\Auth\Domain\Exception;

use App\Shared\Domain\ValueObject\Email;

final class EmailAlreadyExistsException extends \DomainException
{
    public static function create(Email $email): self
    {
        return new self(
            sprintf('User with email "%s" already exists.', $email->getValue())
        );
    }
}
