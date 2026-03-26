<?php

declare(strict_types=1);

namespace App\Crm\Infrastructure\Repository;

use App\Crm\Domain\Entity\Internal\Address;
use App\Crm\Domain\Enums\AddressType;
use App\Crm\Domain\Repository\AddressRepositoryInterface;
use App\Crm\Domain\ValueObject\AdditionalInfo;
use App\Crm\Domain\ValueObject\ApartmentNumber;
use App\Crm\Domain\ValueObject\City;
use App\Crm\Domain\ValueObject\Country;
use App\Crm\Domain\ValueObject\HouseNumber;
use App\Crm\Domain\ValueObject\IsActive;
use App\Crm\Domain\ValueObject\IsPrimary;
use App\Crm\Domain\ValueObject\Latitude;
use App\Crm\Domain\ValueObject\Longitude;
use App\Crm\Domain\ValueObject\PostalCode;
use App\Crm\Domain\ValueObject\StateProvince;
use App\Crm\Domain\ValueObject\Street;
use App\Crm\Infrastructure\Model\AddressModel;
use App\Shared\Domain\ValueObject\Uuid;

final class EloquentAddressRepository implements AddressRepositoryInterface
{
    public function findById(Uuid $id): ?Address
    {
        $model = AddressModel::find($id->getValue());

        if (!$model) {
            return null;
        }

        return $this->toEntity($model);
    }

    public function findByClientUuid(Uuid $clientUuid): array
    {
        return AddressModel::where('client_uuid', $clientUuid->getValue())
            ->orderBy('is_primary', 'desc')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn (AddressModel $model) => $this->toEntity($model))
            ->toArray();
    }

    public function save(Address $address): void
    {
        $data = [
            'client_uuid' => $address->getClientUuid()->getValue(),
            'street' => $address->getStreet()->getValue(),
            'postal_code' => $address->getPostalCode()->getValue(),
            'city' => $address->getCity()->getValue(),
            'state_province' => $address->getStateProvince()->getValue(),
            'country' => $address->getCountry()->getValue(),
            'additional_info' => $address->getAdditionalInfo()->getValue(),
            'house_number' => $address->getHouseNumber()->getValue(),
            'apartment_number' => $address->getApartmentNumber()->getValue(),
            'type' => $address->getType()->value,
            'is_primary' => $address->isPrimary()->toBool(),
            'is_active' => $address->isActive()->toBool(),
            'latitude' => $address->getLatitude()?->toFloat(),
            'longitude' => $address->getLongitude()?->toFloat(),
            'added_at' => $address->getAddedAt()->format('Y-m-d H:i:s'),
        ];

        if ($address->getId() === null) {
            $model = AddressModel::create($data);
            $address->setId(Uuid::fromString($model->id));
        } else {
            AddressModel::where('id', $address->getId()->getValue())->update($data);
        }
    }

    public function delete(Address $address): void
    {
        if ($address->getId() !== null) {
            AddressModel::destroy($address->getId()->getValue());
        }
    }

    private function toEntity(AddressModel $model): Address
    {
        $entity = Address::fromDatabase(
            Uuid::fromString($model->client_uuid),
            Street::fromString($model->street),
            PostalCode::fromString($model->postal_code),
            City::fromString($model->city),
            StateProvince::fromString($model->state_province),
            Country::fromString($model->country),
            AdditionalInfo::fromString($model->additional_info),
            HouseNumber::fromString($model->house_number),
            ApartmentNumber::fromString($model->apartment_number),
            AddressType::from($model->type),
            IsPrimary::fromBool($model->is_primary),
            IsActive::fromBool($model->is_active),
            $model->latitude !== null ? Latitude::fromFloat($model->latitude) : null,
            $model->longitude !== null ? Longitude::fromFloat($model->longitude) : null,
            $model->added_at ? \DateTimeImmutable::createFromMutable($model->added_at) : null,
            $model->created_at ? \DateTimeImmutable::createFromMutable($model->created_at) : null,
            $model->updated_at ? \DateTimeImmutable::createFromMutable($model->updated_at) : null
        );

        $entity->setId(Uuid::fromString($model->id));

        return $entity;
    }
}
