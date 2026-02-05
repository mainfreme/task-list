<?php

declare(strict_types=1);

namespace App\Auth\Domain\Entity;

use App\Auth\Application\DTO\UserDTO;
use App\Auth\Domain\Enum\UserRoleEnum;
use App\Shared\Domain\ValueObject\Email;
use App\Shared\Domain\ValueObject\Uuid;

final class User
{
    private function __construct(
        private Uuid $id,
        private string $name,
        private Email $email,
        private string $password,
        private UserRoleEnum $role,
        private ?\DateTimeImmutable $createdAt = null,
        private ?\DateTimeImmutable $updatedAt = null
    ) {
        $this->createdAt = $this->createdAt ?? new \DateTimeImmutable();
        $this->updatedAt = $this->updatedAt ?? new \DateTimeImmutable();
    }

    public static function create(
        string $name,
        Email $email,
        string $password,
        UserRoleEnum $role = UserRoleEnum::USER
    ): self {
        return new self(
            id: Uuid::generate(),
            name: $name,
            email: $email,
            password: password_hash($password, PASSWORD_BCRYPT),
            role: $role
        );
    }

    public static function fromDatabase(
        Uuid $id,
        string $name,
        Email $email,
        string $password,
        UserRoleEnum $role,
        ?\DateTimeImmutable $createdAt = null,
        ?\DateTimeImmutable $updatedAt = null
    ): self {
        return new self(
            id: $id,
            name: $name,
            email: $email,
            password: $password,
            role: $role,
            createdAt: $createdAt,
            updatedAt: $updatedAt
        );
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function canManageUsers(): bool
    {
        return $this->role === UserRoleEnum::ADMIN;
    }

    public function getRole(): UserRoleEnum
    {
        return $this->role;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->getValue(),
            'name' => $this->name,
            'email' => $this->email->getValue(),
            'role' => $this->role->value,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s'),
        ];
    }

    public function toDTO(): UserDTO
    {
        return new UserDTO(
            id: $this->id,
            name: $this->name,
            email: $this->email,
            password: $this->password,
            role: $this->role,
        );
    }
}
