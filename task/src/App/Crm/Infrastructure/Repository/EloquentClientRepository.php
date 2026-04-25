<?php

declare(strict_types=1);

namespace App\Crm\Infrastructure\Repository;

use App\Crm\Domain\Aggregate\CrmClientAggregate;
use App\Crm\Domain\Enums\ClientStatus;
use App\Crm\Domain\Exception\ClientNotFoundException;
use App\Crm\Domain\Repository\ClientRepositoryInterface;
use App\Crm\Domain\ValueObject\ClientName;
use App\Crm\Domain\ValueObject\ClientNotes;
use App\Crm\Domain\ValueObject\ClientRating;
use App\Crm\Domain\ValueObject\ClientSource;
use App\Crm\Domain\ValueObject\Country;
use App\Crm\Domain\ValueObject\IsCompany;
use App\Crm\Domain\ValueObject\IsDeleted;
use App\Crm\Domain\ValueObject\Nip;
use App\Crm\Domain\ValueObject\Pesel;
use App\Crm\Domain\ValueObject\Regon;
use App\Crm\Infrastructure\Model\ClientModel;
use App\Shared\Domain\ValueObject\Uuid;

final class EloquentClientRepository implements ClientRepositoryInterface
{
    public function findById(Uuid $id): CrmClientAggregate
    {
        $model = ClientModel::find($id->getValue());

        if (!$model) {
            throw ClientNotFoundException::byId($id->getValue());
        }

        return $this->toAggregate($model);
    }

    public function findAll(int $limit = 50, int $offset = 0): array
    {
        return ClientModel::limit($limit)
            ->offset($offset)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn (ClientModel $model) => $this->toAggregate($model))
            ->toArray();
    }

    public function findByStatus(ClientStatus $status, int $limit = 50, int $offset = 0): array
    {
        return ClientModel::where('status', $status->value)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->get()
            ->map(fn (ClientModel $model) => $this->toAggregate($model))
            ->toArray();
    }

    public function count(): int
    {
        return ClientModel::count();
    }

    public function countByStatus(ClientStatus $status): int
    {
        return ClientModel::where('status', $status->value)->count();
    }

    public function save(CrmClientAggregate $client): void
    {
        $data = [
            'id' => $client->getId()->getValue(),
            'address_uuid' => $client->getAddressUuid()?->getValue(),
            'name' => $client->getName()->getValue(),
            'nip' => $client->getNip()?->getValue(),
            'regon' => $client->getRegon()?->toString(),
            'pesel' => $client->getPesel()?->toString(),
            'country' => $client->getCountry()->getValue(),
            'status' => $client->getStatus()->value,
            'source' => $client->getSource()?->toString(),
            'rating' => $client->getRating()?->toInt(),
            'last_contacted_at' => $client->getLastContactedAt()?->format('Y-m-d H:i:s'),
            'next_contact_at' => $client->getNextContactAt()?->format('Y-m-d H:i:s'),
            'notes' => $client->getNotes()?->toString(),
            'is_delete' => $client->isDelete()->toBool(),
            'is_company' => $client->getIsCompany()->toBool(),
        ];

        $exists = ClientModel::where('id', $client->getId()->getValue())->exists();

        if (!$exists) {
            ClientModel::create($data);
        } else {
            unset($data['id']);
            ClientModel::where('id', $client->getId()->getValue())->update($data);
        }
    }

    public function softDelete(CrmClientAggregate $client): void
    {
        $model = ClientModel::find($client->getId()->getValue());

        if (!$model) {
            throw ClientNotFoundException::byId($client->getId()->getValue());
        }

        ClientModel::where('id', $client->getId()->getValue())->update([
            'deleted_at' => now(),
            'is_delete' => true,
        ]);
    }

    private function toAggregate(ClientModel $model): CrmClientAggregate
    {
        return CrmClientAggregate::reconstitute(
            id: Uuid::fromString($model->id),
            name: ClientName::fromString($model->name),
            nip: $model->nip !== null && $model->nip !== '' ? Nip::tryFrom($model->nip) : null,
            country: Country::fromString($model->country),
            status: ClientStatus::from($model->status),
            isCompany: IsCompany::fromBool($model->is_company),
            regon: $model->regon ? Regon::fromString($model->regon) : null,
            pesel: $model->pesel ? Pesel::fromString($model->pesel) : null,
            source: $model->source ? ClientSource::fromString($model->source) : null,
            rating: $model->rating !== null ? ClientRating::fromInt($model->rating) : null,
            notes: ClientNotes::fromString($model->notes),
            lastContactedAt: $model->last_contacted_at ? \DateTimeImmutable::createFromMutable($model->last_contacted_at) : null,
            nextContactAt: $model->next_contact_at ? \DateTimeImmutable::createFromMutable($model->next_contact_at) : null,
            addressUuid: $model->address_uuid ? Uuid::fromString($model->address_uuid) : null,
            addresses: [],
            contacts: [],
            tags: [],
            clientNote: null,
            accounts: [],
            isDeleted: IsDeleted::fromBool($model->is_delete),
            createdAt: \DateTimeImmutable::createFromMutable($model->created_at),
            updatedAt: \DateTimeImmutable::createFromMutable($model->updated_at),
        );
    }
}
