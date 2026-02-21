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

/**
 * DTO for creating a new client aggregate
 */
final readonly class ClientDto
{
    public function __construct(
        public ClientName $name,
        public Nip $nip,
        public Country $country,
        public IsCompany $isCompany,
        public ?Regon $regon = null,
        public ?Pesel $pesel = null,
        public ?ClientSource $source = null,
        public ?ClientRating $rating = null,
        public ?ClientNotes $notes = null,
        public ClientStatus $status = ClientStatus::LEAD
    ) {
    }
}
