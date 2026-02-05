<?php

declare(strict_types=1);

namespace App\Auth\Application\Command\RegisterUser;

use App\Shared\Domain\ValueObject\Email;

final class RegisterUserCommand
{
    public function __construct(
        public readonly string $name,
        public readonly Email $email,
        public readonly string $password,
    ) {
    }
}
