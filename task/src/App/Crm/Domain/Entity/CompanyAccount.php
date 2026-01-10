<?php

declare(strict_types=1);

namespace App\Crm\Domain\Entity;

final class CompanyAccount
{
    private ?string $id = null;

    public function __construct(
        private string $clientUuid,
        private string $name,
        private string $number,
        private string $swiftCode,
        private string $iban,
        private string $bic,
        private string $accountName,
        private ?string $addressUuid = null,
        private bool $isActive = true,
        private bool $isPrimary = false,
        private ?\DateTimeImmutable $createdAt = null,
        private ?\DateTimeImmutable $updatedAt = null
    ) {
        $this->createdAt = $this->createdAt ?? new \DateTimeImmutable();
        $this->updatedAt = $this->updatedAt ?? new \DateTimeImmutable();
    }

    public static function create(
        string $clientUuid,
        string $name,
        string $number,
        string $swiftCode,
        string $iban,
        string $bic,
        string $accountName,
        ?string $addressUuid = null,
        bool $isPrimary = false
    ): self {
        return new self(
            $clientUuid,
            $name,
            $number,
            $swiftCode,
            $iban,
            $bic,
            $accountName,
            $addressUuid,
            true,
            $isPrimary
        );
    }

    public static function fromDatabase(
        string $clientUuid,
        string $name,
        string $number,
        string $swiftCode,
        string $iban,
        string $bic,
        string $accountName,
        bool $isActive = true,
        bool $isPrimary = false,
        ?string $addressUuid = null,
        ?\DateTimeImmutable $createdAt = null,
        ?\DateTimeImmutable $updatedAt = null
    ): self {
        return new self(
            $clientUuid,
            $name,
            $number,
            $swiftCode,
            $iban,
            $bic,
            $accountName,
            $addressUuid,
            $isActive,
            $isPrimary,
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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
        $this->touch();
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function setNumber(string $number): void
    {
        $this->number = $number;
        $this->touch();
    }

    public function getSwiftCode(): string
    {
        return $this->swiftCode;
    }

    public function setSwiftCode(string $swiftCode): void
    {
        $this->swiftCode = $swiftCode;
        $this->touch();
    }

    public function getIban(): string
    {
        return $this->iban;
    }

    public function setIban(string $iban): void
    {
        $this->iban = $iban;
        $this->touch();
    }

    public function getBic(): string
    {
        return $this->bic;
    }

    public function setBic(string $bic): void
    {
        $this->bic = $bic;
        $this->touch();
    }

    public function getAccountName(): string
    {
        return $this->accountName;
    }

    public function setAccountName(string $accountName): void
    {
        $this->accountName = $accountName;
        $this->touch();
    }

    public function getAddressUuid(): ?string
    {
        return $this->addressUuid;
    }

    public function setAddressUuid(?string $addressUuid): void
    {
        $this->addressUuid = $addressUuid;
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

    public function isPrimary(): bool
    {
        return $this->isPrimary;
    }

    public function setPrimary(bool $isPrimary): void
    {
        $this->isPrimary = $isPrimary;
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
