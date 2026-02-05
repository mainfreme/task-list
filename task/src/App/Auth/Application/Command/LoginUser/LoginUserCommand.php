<?php

declare(strict_types=1);

namespace App\Auth\Application\Command\LoginUser;

use App\Shared\Domain\ValueObject\Email;

final class LoginUserCommand
{
    public function __construct(
        public readonly Email $email,
        public readonly string $password,
    ) {
    }
}
