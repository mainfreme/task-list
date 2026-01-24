<?php

declare(strict_types=1);

namespace App\Crm\Domain\Entity\Internal;

use App\Crm\Domain\ValueObject\AccountName;
use App\Crm\Domain\ValueObject\AccountNumber;
use App\Crm\Domain\ValueObject\SwiftCode;
use App\Crm\Domain\ValueObject\Iban;
use App\Crm\Domain\ValueObject\Bic;
use App\Crm\Domain\ValueObject\CompanyAccountName;
use App\Crm\Domain\ValueObject\IsActive;
use App\Crm\Domain\ValueObject\IsPrimary;
use App\Crm\Domain\ValueObject\Uuid\AccountId;
use App\Crm\Domain\ValueObject\Uuid\ClientId;
use App\Crm\Domain\ValueObject\Uuid\AddressId;

/**
 * @internal
 */
final class CompanyAccount
{
    private ?AccountId $id = null;

    public function __construct(
        private ClientId $clientUuid,
        private AccountName $name,
        private AccountNumber $number,
        private SwiftCode $swiftCode,
        private Iban $iban,
        private Bic $bic,
        private CompanyAccountName $accountName,
        private ?AddressId $addressUuid = null,
        private IsActive $isActive = new IsActive(true),
        private IsPrimary $isPrimary = new IsPrimary(false),
        private ?\DateTimeImmutable $createdAt = null,
        private ?\DateTimeImmutable $updatedAt = null
    ) {
        $this->createdAt = $this->createdAt ?? new \DateTimeImmutable();
        $this->updatedAt = $this->updatedAt ?? new \DateTimeImmutable();
    }

    public static function create(
        ClientId $clientUuid,
        AccountName $name,
        AccountNumber $number,
        SwiftCode $swiftCode,
        Iban $iban,
        Bic $bic,
        CompanyAccountName $accountName,
        ?AddressId $addressUuid = null,
        IsPrimary $isPrimary = new IsPrimary(false)
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
            new IsActive(true),
            $isPrimary
        );
    }

    public static function fromDatabase(
        ClientId $clientUuid,
        AccountName $name,
        AccountNumber $number,
        SwiftCode $swiftCode,
        Iban $iban,
        Bic $bic,
        CompanyAccountName $accountName,
        IsActive $isActive,
        IsPrimary $isPrimary,
        ?AddressId $addressUuid = null,
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

    public function getId(): ?AccountId
    {
        return $this->id;
    }

    public function setId(AccountId $id): void
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

    public function getName(): AccountName
    {
        return $this->name;
    }

    public function setName(AccountName $name): void
    {
        $this->name = $name;
        $this->touch();
    }

    public function getNumber(): AccountNumber
    {
        return $this->number;
    }

    public function setNumber(AccountNumber $number): void
    {
        $this->number = $number;
        $this->touch();
    }

    public function getSwiftCode(): SwiftCode
    {
        return $this->swiftCode;
    }

    public function setSwiftCode(SwiftCode $swiftCode): void
    {
        $this->swiftCode = $swiftCode;
        $this->touch();
    }

    public function getIban(): Iban
    {
        return $this->iban;
    }

    public function setIban(Iban $iban): void
    {
        $this->iban = $iban;
        $this->touch();
    }

    public function getBic(): Bic
    {
        return $this->bic;
    }

    public function setBic(Bic $bic): void
    {
        $this->bic = $bic;
        $this->touch();
    }

    public function getAccountName(): CompanyAccountName
    {
        return $this->accountName;
    }

    public function setAccountName(CompanyAccountName $accountName): void
    {
        $this->accountName = $accountName;
        $this->touch();
    }

    public function getAddressUuid(): ?AddressId
    {
        return $this->addressUuid;
    }

    public function setAddressUuid(?AddressId $addressUuid): void
    {
        $this->addressUuid = $addressUuid;
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

    public function isPrimary(): IsPrimary
    {
        return $this->isPrimary;
    }

    public function setPrimary(IsPrimary $isPrimary): void
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
