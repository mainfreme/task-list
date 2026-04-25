<?php

declare(strict_types=1);

namespace App\Ops\Application\Command\RecordDeployFailure;

use App\Ops\Domain\ValueObject\DeployContainerName;
use App\Ops\Domain\ValueObject\DeployHostname;
use App\Ops\Domain\ValueObject\DeployMessage;
use App\Ops\Domain\ValueObject\DeployProjectName;
use App\Ops\Domain\ValueObject\DeployRepository;
use App\Ops\Domain\ValueObject\DeployStage;

final class RecordDeployFailureCommand
{
    public function __construct(
        public readonly DeployProjectName $project,
        public readonly DeployRepository $repository,
        public readonly ?DeployContainerName $container,
        public readonly DeployStage $stage,
        public readonly DeployMessage $message,
        public readonly ?DeployHostname $hostname,
    ) {
    }
}
