<?php

declare(strict_types=1);

namespace App\Ops\Infrastructure\Repository;

use App\Ops\Domain\Entity\DeployFailure;
use App\Ops\Domain\Repository\DeployFailureRepositoryInterface;
use App\Ops\Infrastructure\Model\DeployFailureModel;
use App\Shared\Domain\ValueObject\Uuid;

final class EloquentDeployFailureRepository implements DeployFailureRepositoryInterface
{
    public function save(DeployFailure $deployFailure): void
    {
        $data = [
            'project' => $deployFailure->getProject()->getValue(),
            'repository' => $deployFailure->getRepository()->getValue(),
            'container' => $deployFailure->getContainer()?->getValue(),
            'stage' => $deployFailure->getStage()->value,
            'message' => $deployFailure->getMessage()->getValue(),
            'hostname' => $deployFailure->getHostname()?->getValue(),
        ];

        if ($deployFailure->getId() === null) {
            $model = DeployFailureModel::create($data);
            $deployFailure->setId(Uuid::fromString($model->id));
        } else {
            DeployFailureModel::where('id', $deployFailure->getId()->getValue())->update($data);
        }
    }
}
