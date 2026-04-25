<?php

declare(strict_types=1);

namespace App\Ops\Domain\Repository;

use App\Ops\Domain\Entity\DeployFailure;

interface DeployFailureRepositoryInterface
{
    public function save(DeployFailure $deployFailure): void;
}
