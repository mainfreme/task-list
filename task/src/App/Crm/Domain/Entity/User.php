<?php

declare(strict_types=1);

namespace App\Crm\Domain\Entity;

use App\Crm\Domain\ValueObject\Email;
use App\Crm\Domain\ValueObject\Password;
use App\Shared\Domain\ValueObject\Uuid;

final class User
{
    private ?Uuid $id = null;

    public function __construct(
        private Email $email,
        private Password $password,
        private array $roles = [],
        private ?string $rememberToken = null,
        private ?\DateTimeImmutable $createdAt = null,
        private ?\DateTimeImmutable $updatedAt = null
    ) {
        $this->createdAt = $this->createdAt ?? new \DateTimeImmutable();
        $this->updatedAt = $this->updatedAt ?? new \DateTimeImmutable();
    }

    public static function create(
        Email $email,
        Password $password,
        array $roles = []
    ): self {
        return new self($email, $password, $roles);
    }

    public static function fromDatabase(
        Email $email,
        Password $password,
        array $roles = [],
        ?string $rememberToken = null,
        ?\DateTimeImmutable $createdAt = null,
        ?\DateTimeImmutable $updatedAt = null
    ): self {
        return new self($email, $password, $roles, $rememberToken, $createdAt, $updatedAt);
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function setId(Uuid $id): void
    {
        $this->id = $id;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function setEmail(Email $email): void
    {
        $this->email = $email;
        $this->touch();
    }

    public function getPassword(): Password
    {
        return $this->password;
    }

    public function setPassword(Password $password): void
    {
        $this->password = $password;
        $this->touch();
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
        $this->touch();
    }

    public function getRememberToken(): ?string
    {
        return $this->rememberToken;
    }

    public function setRememberToken(?string $rememberToken): void
    {
        $this->rememberToken = $rememberToken;
        $this->touch();
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    private function touch(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
}
