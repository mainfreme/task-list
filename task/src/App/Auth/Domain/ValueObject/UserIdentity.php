<?php

declare(strict_types=1);

namespace App\Auth\Domain\ValueObject;

use App\Auth\Domain\Entity\User;
use App\Auth\Domain\Enum\UserRoleEnum;
use App\Shared\Domain\ValueObject\Email;
use App\Shared\Domain\ValueObject\Uuid;

/**
 * Publiczna tożsamość użytkownika (bez sekretów) — np. do generowania JWT.
 */
final readonly class UserIdentity
{
    public function __construct(
        public Uuid $id,
        public string $name,
        public Email $email,
        public UserRoleEnum $role,
    ) {
    }

    public static function fromUser(User $user): self
    {
        return new self(
            id: $user->getId(),
            name: $user->getName(),
            email: $user->getEmail(),
            role: $user->getRole(),
        );
    }
}
