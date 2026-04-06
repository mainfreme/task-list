<?php

declare(strict_types=1);

namespace App\ApplicationManager\Infrastructure\Repository;

use App\ApplicationManager\Domain\Entity\ApplicationManager;
use App\ApplicationManager\Domain\Event\ApplicationManagerPersistedEvent;
use App\ApplicationManager\Domain\Exception\ApplicationManagerNotFoundException;
use App\ApplicationManager\Domain\Repository\ApplicationManagerRepositoryInterface;
use App\ApplicationManager\Domain\ValueObject\ApiKey;
use App\ApplicationManager\Infrastructure\Eloquent\ApplicationManagerModel;
use App\ApplicationManager\Infrastructure\Mapper\ApplicationManagerEntityMapper;
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

        return ApplicationManagerEntityMapper::fromModel($model);
    }

    public function findByApiKey(ApiKey $apiKey): ApplicationManager
    {
        $models = ApplicationManagerModel::where('is_active', true)->get();

        foreach ($models as $model) {
            if (Hash::check($apiKey->value(), $model->api_key_hash)) {
                return ApplicationManagerEntityMapper::fromModel($model);
            }
        }

        throw ApplicationManagerNotFoundException::byApiKey($apiKey->value());
    }

    public function save(ApplicationManager $applicationManager): void
    {
        $wasNew = $applicationManager->getId() === null;

        $requestUrl = $applicationManager->getRequestUrl();
        $ipWhitelist = $applicationManager->getIpWhitelist();

        $data = [
            'name' => $applicationManager->getName()->getValue(),
            'api_key_hash' => Hash::make($applicationManager->getApiKey()->value()),
            'request_url' => $requestUrl?->getValue(),
            'is_active' => $applicationManager->isActive(),
            'ip_whitelist' => $ipWhitelist?->toArray(),
        ];

        if ($wasNew) {
            $model = ApplicationManagerModel::create($data);
            $applicationManager->setId(Uuid::fromString($model->id));
        } else {
            ApplicationManagerModel::where('id', $applicationManager->getId()->getValue())->update($data);
        }

        $id = $applicationManager->getId();
        if ($id !== null) {
            event(new ApplicationManagerPersistedEvent(
                $id->getValue(),
                $wasNew ? ApplicationManagerPersistedEvent::OPERATION_CREATED : ApplicationManagerPersistedEvent::OPERATION_UPDATED
            ));
        }
    }

    public function delete(ApplicationManager $applicationManager): void
    {
        if ($applicationManager->getId() !== null) {
            $id = $applicationManager->getId()->getValue();
            ApplicationManagerModel::destroy($id);
            event(new ApplicationManagerPersistedEvent($id, ApplicationManagerPersistedEvent::OPERATION_DELETED));
        }
    }

    public function findAll(): array
    {
        return ApplicationManagerModel::all()
            ->map(fn (ApplicationManagerModel $model) => ApplicationManagerEntityMapper::fromModel($model))
            ->toArray();
    }
}
