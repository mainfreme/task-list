<?php

declare(strict_types=1);

namespace App\Crm\Domain\Entity;

use App\Crm\Domain\ValueObject\ContactType;
use App\Crm\Domain\ValueObject\ContactRole;

final class ClientContact
{
    private ?string $id = null;

    public function __construct(
        private string $clientUuid,
        private ContactType $type,
        private string $value,
        private ?string $countryPrefix = null,
        private ?ContactRole $contactRole = null,
        private bool $isPrimary = false,
        private bool $isActive = true,
        private bool $isVerified = false,
        private ?\DateTimeImmutable $createdAt = null,
        private ?\DateTimeImmutable $updatedAt = null
    ) {
        $this->createdAt = $this->createdAt ?? new \DateTimeImmutable();
        $this->updatedAt = $this->updatedAt ?? new \DateTimeImmutable();
    }

    public static function create(
        string $clientUuid,
        ContactType $type,
        string $value,
        ?string $countryPrefix = null,
        ?ContactRole $contactRole = null,
        bool $isPrimary = false
    ): self {
        return new self(
            $clientUuid,
            $type,
            $value,
            $countryPrefix,
            $contactRole,
            $isPrimary
        );
    }

    public static function fromDatabase(
        string $clientUuid,
        ContactType $type,
        string $value,
        bool $isPrimary = false,
        bool $isActive = true,
        bool $isVerified = false,
        ?string $countryPrefix = null,
        ?ContactRole $contactRole = null,
        ?\DateTimeImmutable $createdAt = null,
        ?\DateTimeImmutable $updatedAt = null
    ): self {
        return new self(
            $clientUuid,
            $type,
            $value,
            $countryPrefix,
            $contactRole,
            $isPrimary,
            $isActive,
            $isVerified,
            $createdAt,
            $updatedAt
        );
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getClientUuid(): string
    {
        return $this->clientUuid;
    }

    public function setClientUuid(string $clientUuid): void
    {
        $this->clientUuid = $clientUuid;
        $this->touch();
    }

    public function getType(): ContactType
    {
        return $this->type;
    }

    public function setType(ContactType $type): void
    {
        $this->type = $type;
        $this->touch();
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
        $this->touch();
    }

    public function getCountryPrefix(): ?string
    {
        return $this->countryPrefix;
    }

    public function setCountryPrefix(?string $countryPrefix): void
    {
        $this->countryPrefix = $countryPrefix;
        $this->touch();
    }

    public function getContactRole(): ?ContactRole
    {
        return $this->contactRole;
    }

    public function setContactRole(?ContactRole $contactRole): void
    {
        $this->contactRole = $contactRole;
        $this->touch();
    }

    public function isPrimary(): bool
    {
        return $this->isPrimary;
    }

    public function setPrimary(bool $isPrimary): void
    {
        $this->isPrimary = $isPrimary;
        $this->touch();
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function activate(): void
    {
        $this->isActive = true;
        $this->touch();
    }

    public function deactivate(): void
    {
        $this->isActive = false;
        $this->touch();
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function verify(): void
    {
        $this->isVerified = true;
        $this->touch();
    }

    public function unverify(): void
    {
        $this->isVerified = false;
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
