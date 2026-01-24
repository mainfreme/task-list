<?php

declare(strict_types=1);

namespace App\ApplicationManager\Application\Command\CreateApplicationManager;

use App\ApplicationManager\Domain\ValueObject\ApplicationName;
use App\ApplicationManager\Domain\ValueObject\IpWhitelist;
use App\ApplicationManager\Domain\ValueObject\RequestUrl;

final class CreateApplicationManagerCommand
{
    public function __construct(
        public readonly ApplicationName $name,
        public readonly ?RequestUrl $requestUrl = null,
        public readonly bool $isActive = true,
        public readonly ?IpWhitelist $ipWhitelist = null,
    ) {
    }
}
