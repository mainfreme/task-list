<?php

declare(strict_types=1);

namespace App\Auth\Application\DTO;

use App\Auth\Domain\Entity\User;
use App\Auth\Domain\Enum\UserRoleEnum;
use App\Shared\Domain\ValueObject\Email;
use App\Shared\Domain\ValueObject\Uuid;

final class UserDTO
{
    public function __construct(
        public readonly Uuid $id,
        public readonly string $name,
        public readonly Email $email,
        public readonly string $password,
        public readonly UserRoleEnum $role,
    ) {
    }

    public static function fromUser(User $user): self
    {
        return new self(
            id: $user->getId(),
            name: $user->getName(),
            email: $user->getEmail(),
            password: $user->getPassword(),
            role: $user->getRole(),
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] instanceof Uuid ? $data['id'] : Uuid::fromString($data['id']),
            name: $data['name'],
            email: $data['email'] instanceof Email ? $data['email'] : Email::fromString($data['email']),
            password: $data['password'],
            role: $data['role'] instanceof UserRoleEnum ? $data['role'] : UserRoleEnum::from($data['role']),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id->getValue(),
            'name' => $this->name,
            'email' => $this->email->getValue(),
            'role' => $this->role->value,
        ];
    }
}
