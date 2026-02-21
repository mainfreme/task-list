<?php

declare(strict_types=1);

namespace App\Crm\Domain\Entity\Internal;

use App\Crm\Domain\Enums\ContactRole;
use App\Crm\Domain\Enums\ContactType;
use App\Crm\Domain\ValueObject\ContactValue;
use App\Crm\Domain\ValueObject\CountryPrefix;
use App\Crm\Domain\ValueObject\IsActive;
use App\Crm\Domain\ValueObject\IsPrimary;
use App\Crm\Domain\ValueObject\IsVerified;
use App\Crm\Domain\ValueObject\Uuid\ClientId;
use App\Crm\Domain\ValueObject\Uuid\ContactId;

/**
 * @internal
 */
final class ClientContact
{
    private ?ContactId $id = null;

    public function __construct(
        private ClientId $clientUuid,
        private ContactType $type,
        private ContactValue $value,
        private ?CountryPrefix $countryPrefix = null,
        private ?ContactRole $contactRole = null,
        private IsPrimary $isPrimary = new IsPrimary(false),
        private IsActive $isActive = new IsActive(true),
        private IsVerified $isVerified = new IsVerified(false),
        private ?\DateTimeImmutable $createdAt = null,
        private ?\DateTimeImmutable $updatedAt = null
    ) {
        $this->createdAt = $this->createdAt ?? new \DateTimeImmutable();
        $this->updatedAt = $this->updatedAt ?? new \DateTimeImmutable();
    }

    public static function create(
        ClientId $clientUuid,
        ContactType $type,
        ContactValue $value,
        ?CountryPrefix $countryPrefix = null,
        ?ContactRole $contactRole = null,
        IsPrimary $isPrimary = new IsPrimary(false)
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
        ClientId $clientUuid,
        ContactType $type,
        ContactValue $value,
        IsPrimary $isPrimary,
        IsActive $isActive,
        IsVerified $isVerified,
        ?CountryPrefix $countryPrefix = null,
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

    public function getId(): ?ContactId
    {
        return $this->id;
    }

    public function setId(ContactId $id): void
    {
        $this->id = $id;
    }

    public function getClientUuid(): ClientId
    {
        return $this->clientUuid;
    }

    public function setClientUuid(ClientId $clientUuid): void
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

    public function getValue(): ContactValue
    {
        return $this->value;
    }

    public function setValue(ContactValue $value): void
    {
        $this->value = $value;
        $this->touch();
    }

    public function getCountryPrefix(): ?CountryPrefix
    {
        return $this->countryPrefix;
    }

    public function setCountryPrefix(?CountryPrefix $countryPrefix): void
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

    public function isPrimary(): IsPrimary
    {
        return $this->isPrimary;
    }

    public function setPrimary(IsPrimary $isPrimary): void
    {
        $this->isPrimary = $isPrimary;
        $this->touch();
    }

    public function isActive(): IsActive
    {
        return $this->isActive;
    }

    public function activate(): void
    {
        $this->isActive = IsActive::fromBool(true);
        $this->touch();
    }

    public function deactivate(): void
    {
        $this->isActive = IsActive::fromBool(false);
        $this->touch();
    }

    public function isVerified(): IsVerified
    {
        return $this->isVerified;
    }

    public function verify(): void
    {
        $this->isVerified = IsVerified::fromBool(true);
        $this->touch();
    }

    public function unverify(): void
    {
        $this->isVerified = IsVerified::fromBool(false);
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
