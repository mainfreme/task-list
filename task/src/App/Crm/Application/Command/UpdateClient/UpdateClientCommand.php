<?php

declare(strict_types=1);

namespace App\Crm\Application\Command\UpdateClient;

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

final class UpdateClientCommand
{
    public function __construct(
        public readonly Uuid $id,
        public readonly ?ClientName $name = null,
        public readonly ?Nip $nip = null,
        public readonly bool $nipCleared = false,
        public readonly ?Country $country = null,
        public readonly ?ClientStatus $status = null,
        public readonly ?IsCompany $isCompany = null,
        public readonly ?Regon $regon = null,
        public readonly ?Pesel $pesel = null,
        public readonly ?ClientSource $source = null,
        public readonly ?ClientRating $rating = null,
        public readonly ?ClientNotes $notes = null,
        public readonly ?Uuid $addressUuid = null,
        /** @var list<string> Fields to explicitly clear (e.g. when client sends address_uuid: null) */
        public readonly array $clearFields = [],
    ) {
    }
}
