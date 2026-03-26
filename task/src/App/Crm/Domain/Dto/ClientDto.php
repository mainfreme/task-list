<?php

declare(strict_types=1);

namespace App\Crm\Domain\Dto;

use App\Crm\Domain\Enums\ClientStatus;
use App\Crm\Domain\ValueObject\ClientName;
use App\Crm\Domain\ValueObject\ClientNotes;
use App\Crm\Domain\ValueObject\ClientRating;
use App\Crm\Domain\ValueObject\ClientSource;
use App\Crm\Domain\ValueObject\Country;
use App\Crm\Domain\ValueObject\IsCompany;
use App\Crm\Domain\ValueObject\Nip;
use App\Crm\Domain\ValueObject\Pesel;
use App\Crm\Domain\ValueObject\Regon;
use App\Shared\Domain\ValueObject\Uuid;

/**
 * DTO for client – used for creating aggregate (input) and API responses (output).
 */
final readonly class ClientDto
{
    public function __construct(
        public ClientName $name,
        public Nip $nip,
        public Country $country,
        public IsCompany $isCompany,
        public ?Uuid $id = null,
        public ?Regon $regon = null,
        public ?Pesel $pesel = null,
        public ?ClientSource $source = null,
        public ?ClientRating $rating = null,
        public ?ClientNotes $notes = null,
        public ClientStatus $status = ClientStatus::LEAD,
        public ?Uuid $addressUuid = null,
        public ?\DateTimeImmutable $lastContactedAt = null,
        public ?\DateTimeImmutable $nextContactAt = null,
        public ?\DateTimeImmutable $createdAt = null,
        public ?\DateTimeImmutable $updatedAt = null,
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id?->getValue(),
            'name' => $this->name->getValue(),
            'nip' => $this->nip->getValue(),
            'country' => $this->country->getValue(),
            'status' => $this->status->value,
            'is_company' => $this->isCompany->toBool(),
            'regon' => $this->regon?->toString(),
            'pesel' => $this->pesel?->toString(),
            'source' => $this->source?->toString(),
            'rating' => $this->rating?->toInt(),
            'notes' => $this->notes?->toString(),
            'address_uuid' => $this->addressUuid?->getValue(),
            'last_contacted_at' => $this->lastContactedAt?->format('Y-m-d H:i:s'),
            'next_contact_at' => $this->nextContactAt?->format('Y-m-d H:i:s'),
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
        ];
    }
}
