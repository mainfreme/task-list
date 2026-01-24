<?php

declare(strict_types=1);

namespace App\ApplicationManager\Application\Command\UpdateApplicationManager;

use App\ApplicationManager\Domain\ValueObject\ApplicationName;
use App\ApplicationManager\Domain\ValueObject\Uuid;
use App\ApplicationManager\Domain\ValueObject\IpWhitelist;
use App\ApplicationManager\Domain\ValueObject\RequestUrl;

final class UpdateApplicationManagerCommand
{
    public function __construct(
        public readonly Uuid $id,
        public readonly ?ApplicationName $name = null,
        public readonly ?RequestUrl $requestUrl = null,
        public readonly ?bool $isActive = null,
        public readonly ?IpWhitelist $ipWhitelist = null,
    ) {
    }
}
