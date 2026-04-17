<?php

declare(strict_types=1);

namespace App\ApplicationManager\UI\Http\Mappers;

use App\ApplicationManager\Application\Command\CreateApplicationManager\CreateApplicationManagerCommand;
use App\ApplicationManager\Domain\ValueObject\ApplicationName;
use App\ApplicationManager\Domain\ValueObject\IpWhitelist;
use App\ApplicationManager\Domain\ValueObject\RequestUrl;
use App\ApplicationManager\UI\Http\Requests\V1\CreateApplicationManagerRequest;
use App\Shared\Infrastructure\Mapper\Attribute\MapField;
use App\Shared\Infrastructure\Mapper\Attribute\MapFrom;

#[MapFrom(CreateApplicationManagerRequest::class)]
final class CreateApplicationManagerCommandMapper
{
    #[MapField]
    public ApplicationName $name;

    #[MapField('request_url')]
    public ?RequestUrl $requestUrl = null;

    #[MapField('is_active')]
    public bool $isActive = true;

    #[MapField('ip_whitelist')]
    public ?IpWhitelist $ipWhitelist = null;

    public function toCommand(): CreateApplicationManagerCommand
    {
        return new CreateApplicationManagerCommand(
            name: $this->name,
            requestUrl: $this->requestUrl,
            isActive: $this->isActive,
            ipWhitelist: $this->ipWhitelist,
        );
    }
}
