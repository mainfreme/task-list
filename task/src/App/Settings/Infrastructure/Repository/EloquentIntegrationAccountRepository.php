<?php

declare(strict_types=1);

namespace App\Settings\Infrastructure\Repository;

use App\Settings\Domain\Entity\IntegrationAccount;
use App\Settings\Domain\Exception\IntegrationAccountNotFoundException;
use App\Settings\Domain\Repository\IntegrationAccountRepositoryInterface;
use App\Settings\Infrastructure\Model\IntegrationAccountModel;
use App\Shared\Domain\ValueObject\Uuid;
use DateTimeImmutable;

final class EloquentIntegrationAccountRepository implements IntegrationAccountRepositoryInterface
{
    public function findById(Uuid $id): IntegrationAccount
    {
        $model = IntegrationAccountModel::find($id->getValue());

        if (!$model) {
            throw IntegrationAccountNotFoundException::byId($id->getValue());
        }

        return $this->toEntity($model);
    }

    public function findAll(): array
    {
        return IntegrationAccountModel::orderBy('name')
            ->get()
            ->map(fn (IntegrationAccountModel $m) => $this->toEntity($m))
            ->all();
    }

    public function save(IntegrationAccount $account): void
    {
        $data = [
            'id' => $account->getId()->getValue(),
            'name' => $account->getName(),
            'enabled' => $account->isEnabled(),
            'external_account_id' => $account->getExternalAccountId(),
            'provider' => $account->getProvider(),
            'credentials' => $account->getCredentials(),
        ];

        $exists = IntegrationAccountModel::where('id', $account->getId()->getValue())->exists();

        if (!$exists) {
            IntegrationAccountModel::create($data);
        } else {
            unset($data['id']);
            IntegrationAccountModel::where('id', $account->getId()->getValue())->update($data);
        }
    }

    public function softDelete(Uuid $id): void
    {
        $model = IntegrationAccountModel::find($id->getValue());

        if (!$model) {
            throw IntegrationAccountNotFoundException::byId($id->getValue());
        }

        $model->delete();
    }

    private function toEntity(IntegrationAccountModel $model): IntegrationAccount
    {
        /** @var array<string, mixed> $credentials */
        $credentials = $model->credentials ?? [];

        return IntegrationAccount::reconstitute(
            id: Uuid::fromString($model->id),
            name: $model->name,
            enabled: (bool) $model->enabled,
            externalAccountId: $model->external_account_id,
            provider: $model->provider,
            credentials: $credentials,
            createdAt: $this->toImmutable($model->created_at),
            updatedAt: $this->toImmutable($model->updated_at),
        );
    }

    private function toImmutable(mixed $value): ?DateTimeImmutable
    {
        if ($value === null) {
            return null;
        }

        return DateTimeImmutable::createFromMutable($value);
    }
}
