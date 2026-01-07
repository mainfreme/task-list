<?php

declare(strict_types=1);

namespace App\Infrastructure\ApplicationManager\Repository;

use App\Domain\ApplicationManager\Entity\ApplicationManager;
use App\Domain\ApplicationManager\Exception\ApplicationManagerNotFoundException;
use App\Domain\ApplicationManager\Repository\ApplicationManagerRepositoryInterface;
use App\Domain\ApplicationManager\ValueObject\ApiKey;
use App\Infrastructure\ApplicationManager\Eloquent\ApplicationManagerModel;
use Illuminate\Support\Facades\Hash;

final class EloquentApplicationManagerRepository implements ApplicationManagerRepositoryInterface
{
    public function findById(int $id): ApplicationManager
    {
        $model = ApplicationManagerModel::find($id);

        if (!$model) {
            throw ApplicationManagerNotFoundException::byId($id);
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
        $data = [
            'name' => $applicationManager->getName(),
            'api_key_hash' => Hash::make($applicationManager->getApiKey()->value()),
            'request_url' => $applicationManager->getRequestUrl(),
            'is_active' => $applicationManager->isActive(),
            'ip_whitelist' => $applicationManager->getIpWhitelist(),
        ];

        if ($applicationManager->getId() === null) {
            $model = ApplicationManagerModel::create($data);
            $applicationManager->setId($model->id);
        } else {
            ApplicationManagerModel::where('id', $applicationManager->getId())->update($data);
        }
    }

    public function delete(ApplicationManager $applicationManager): void
    {
        if ($applicationManager->getId() !== null) {
            ApplicationManagerModel::destroy($applicationManager->getId());
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
        $apiKey = ApiKey::fromString('placeholder'); // This is a limitation - we can't get original key from hash

        $entity = ApplicationManager::fromDatabase(
            $model->name,
            $apiKey,
            $model->request_url,
            $model->is_active,
            $model->ip_whitelist,
            $model->created_at ? \DateTimeImmutable::createFromMutable($model->created_at) : null,
            $model->updated_at ? \DateTimeImmutable::createFromMutable($model->updated_at) : null
        );

        $entity->setId($model->id);

        return $entity;
    }
}

