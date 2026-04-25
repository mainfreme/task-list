<?php

declare(strict_types=1);

namespace App\Crm\UI\Http\Mappers;

use App\Crm\Application\Command\CreateClient\CreateClientCommand;
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
use App\Crm\UI\Http\Mappers\Transformer\BoolToIsCompanyTransformer;
use App\Crm\UI\Http\Mappers\Transformer\NullableStringToNipTransformer;
use App\Crm\UI\Http\Mappers\Transformer\NullableIntToClientRatingTransformer;
use App\Crm\UI\Http\Requests\V1\CreateClientRequest;
use App\Shared\Infrastructure\Mapper\Attribute\MapField;
use App\Shared\Infrastructure\Mapper\Attribute\MapFrom;

#[MapFrom(CreateClientRequest::class)]
final class CreateClientCommandMapper
{
    #[MapField]
    public ClientName $name;

    #[MapField(transformer: NullableStringToNipTransformer::class)]
    public ?Nip $nip = null;

    #[MapField]
    public Country $country;

    #[MapField('is_company', transformer: BoolToIsCompanyTransformer::class)]
    public ?IsCompany $isCompany = null;

    #[MapField]
    public ?Regon $regon = null;

    #[MapField]
    public ?Pesel $pesel = null;

    #[MapField]
    public ?ClientSource $source = null;

    #[MapField(transformer: NullableIntToClientRatingTransformer::class)]
    public ?ClientRating $rating = null;

    #[MapField]
    public ?ClientNotes $notes = null;

    #[MapField]
    public ?ClientStatus $status = null;

    #[MapField('address_uuid')]
    public ?\App\Shared\Domain\ValueObject\Uuid $addressUuid = null;

    public function toCommand(): CreateClientCommand
    {
        return new CreateClientCommand(
            name: $this->name,
            nip: $this->nip,
            country: $this->country,
            isCompany: $this->isCompany ?? IsCompany::fromBool(false),
            regon: $this->regon,
            pesel: $this->pesel,
            source: $this->source,
            rating: $this->rating,
            notes: $this->notes,
            status: $this->status ?? ClientStatus::LEAD,
            addressUuid: $this->addressUuid,
        );
    }
}
