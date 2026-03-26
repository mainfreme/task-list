<?php

declare(strict_types=1);

namespace App\Crm\Application\Command\CreateClient;

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

final class CreateClientCommand
{
    public function __construct(
        public readonly ClientName $name,
        public readonly Nip $nip,
        public readonly Country $country,
        public readonly IsCompany $isCompany,
        public readonly ?Regon $regon = null,
        public readonly ?Pesel $pesel = null,
        public readonly ?ClientSource $source = null,
        public readonly ?ClientRating $rating = null,
        public readonly ?ClientNotes $notes = null,
        public readonly ClientStatus $status = ClientStatus::LEAD,
        public readonly ?Uuid $addressUuid = null,
    ) {
    }
}
