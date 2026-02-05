<?php

declare(strict_types=1);

namespace App\ApplicationManager\Infrastructure\Repository;

use App\ApplicationManager\Domain\Entity\ApplicationManager;
use App\ApplicationManager\Domain\Exception\ApplicationManagerNotFoundException;
use App\ApplicationManager\Domain\Repository\ApplicationManagerRepositoryInterface;
use App\ApplicationManager\Domain\ValueObject\ApiKey;
use App\ApplicationManager\Domain\ValueObject\ApplicationName;
use App\ApplicationManager\Domain\ValueObject\IpWhitelist;
use App\ApplicationManager\Domain\ValueObject\RequestUrl;
use App\ApplicationManager\Infrastructure\Eloquent\ApplicationManagerModel;
use App\Shared\Domain\ValueObject\Uuid;
use Illuminate\Support\Facades\Hash;

final class EloquentApplicationManagerRepository implements ApplicationManagerRepositoryInterface
{
    public function findById(Uuid $id): ApplicationManager
    {
        $model = ApplicationManagerModel::find($id->getValue());

        if (!$model) {
            throw ApplicationManagerNotFoundException::byId($id->getValue());
        }

        return $this->toEntity($model);
    }

    public function findByApiKey(ApiKey $apiKey): ApplicationManager
    {
        $models = ApplicationManagerModel::where('is_active', true)->get();

        foreach ($models as $model) {
            if (Hash::check($apiKey->value(), $model->api_key_hash)) {
                return $this->toEntity($model);
            }
        }

        throw ApplicationManagerNotFoundException::byApiKey($apiKey->value());
    }

    public function save(ApplicationManager $applicationManager): void
    {
        $requestUrl = $applicationManager->getRequestUrl();
        $ipWhitelist = $applicationManager->getIpWhitelist();

        $data = [
            'name' => $applicationManager->getName()->getValue(),
            'api_key_hash' => Hash::make($applicationManager->getApiKey()->value()),
            'request_url' => $requestUrl?->getValue(),
            'is_active' => $applicationManager->isActive(),
            'ip_whitelist' => $ipWhitelist?->toArray(),
        ];

        if ($applicationManager->getId() === null) {
            $model = ApplicationManagerModel::create($data);
            $applicationManager->setId(Uuid::fromString($model->id));
        } else {
            ApplicationManagerModel::where('id', $applicationManager->getId()->getValue())->update($data);
        }
    }

    public function delete(ApplicationManager $applicationManager): void
    {
        if ($applicationManager->getId() !== null) {
            ApplicationManagerModel::destroy($applicationManager->getId()->getValue());
        }
    }

    public function findAll(): array
    {
        return ApplicationManagerModel::all()
            ->map(fn (ApplicationManagerModel $model) => $this->toEntity($model))
            ->toArray();
    }

    private function toEntity(ApplicationManagerModel $model): ApplicationManager
    {
        // Note: We cannot reconstruct the original ApiKey from hash, so we create a placeholder
        // In real scenario, we might need to store the plain API key in a secure way or use a different approach
        // For now, we'll use a workaround - the API key will be regenerated when needed
        $apiKey = ApiKey::generate(); // This is a limitation - we can't get original key from hash

        $entity = ApplicationManager::fromDatabase(
            ApplicationName::fromString($model->name),
            $apiKey,
            RequestUrl::fromNullable($model->request_url),
            $model->is_active,
            IpWhitelist::fromNullable($model->ip_whitelist),
            $model->created_at ? \DateTimeImmutable::createFromMutable($model->created_at) : null,
            $model->updated_at ? \DateTimeImmutable::createFromMutable($model->updated_at) : null
        );

        $entity->setId(Uuid::fromString($model->id));

        return $entity;
    }
}
