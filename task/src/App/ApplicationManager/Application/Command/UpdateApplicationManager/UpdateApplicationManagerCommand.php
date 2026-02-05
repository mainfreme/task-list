<?php

declare(strict_types=1);

namespace App\ApplicationManager\Application\Command\UpdateApplicationManager;

use App\ApplicationManager\Domain\ValueObject\ApplicationName;
use App\ApplicationManager\Domain\ValueObject\IpWhitelist;
use App\ApplicationManager\Domain\ValueObject\RequestUrl;
use App\Shared\Domain\ValueObject\Uuid;

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
