<?php

declare(strict_types=1);

namespace App\Crm\UI\Http\Mappers;

use App\Crm\Application\Command\UpdateClient\UpdateClientCommand;
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
use App\Crm\UI\Http\Requests\V1\UpdateClientRequest;
use App\Shared\Domain\ValueObject\Uuid;

final class UpdateClientCommandMapper
{
    public function map(UpdateClientRequest $request, Uuid $id): UpdateClientCommand
    {
        $validated = $request->validated();
        $clearFields = [];

        $addressUuid = null;
        if (array_key_exists('address_uuid', $validated)) {
            if ($validated['address_uuid'] !== null) {
                $addressUuid = Uuid::fromString($validated['address_uuid']);
            } else {
                $clearFields[] = 'address_uuid';
            }
        }

        $rating = null;
        if (array_key_exists('rating', $validated)) {
            $rating = $validated['rating'] !== null
                ? ClientRating::fromInt((int) $validated['rating'])
                : ClientRating::fromInt(null);
        }

        $nip = null;
        $nipCleared = false;
        if (array_key_exists('nip', $validated)) {
            $rawNip = $validated['nip'];
            if ($rawNip === null || $rawNip === '') {
                $nipCleared = true;
            } else {
                $nip = Nip::tryFrom((string) $rawNip);
            }
        }

        return new UpdateClientCommand(
            id: $id,
            name: isset($validated['name']) ? ClientName::fromString($validated['name']) : null,
            nip: $nip,
            nipCleared: $nipCleared,
            country: isset($validated['country']) ? Country::fromString($validated['country']) : null,
            status: isset($validated['status']) ? ClientStatus::from($validated['status']) : null,
            isCompany: isset($validated['is_company']) ? IsCompany::fromBool((bool) $validated['is_company']) : null,
            regon: array_key_exists('regon', $validated) ? Regon::fromString($validated['regon'] ?? null) : null,
            pesel: array_key_exists('pesel', $validated) ? Pesel::fromString($validated['pesel'] ?? null) : null,
            source: array_key_exists('source', $validated) ? ClientSource::fromString($validated['source'] ?? null) : null,
            rating: $rating,
            notes: array_key_exists('notes', $validated) ? ClientNotes::fromString($validated['notes'] ?? null) : null,
            addressUuid: $addressUuid,
            clearFields: $clearFields,
        );
    }
}
