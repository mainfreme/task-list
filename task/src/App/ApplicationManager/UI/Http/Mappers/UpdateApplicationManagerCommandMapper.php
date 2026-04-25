<?php

declare(strict_types=1);

namespace App\ApplicationManager\UI\Http\Mappers;

use App\ApplicationManager\Application\Command\UpdateApplicationManager\UpdateApplicationManagerCommand;
use App\ApplicationManager\Domain\ValueObject\ApplicationName;
use App\ApplicationManager\Domain\ValueObject\IpWhitelist;
use App\ApplicationManager\Domain\ValueObject\RequestUrl;
use App\ApplicationManager\UI\Http\Requests\V1\UpdateApplicationManagerRequest;
use App\Shared\Domain\ValueObject\Uuid;
use App\Shared\Infrastructure\Mapper\Attribute\MapField;
use App\Shared\Infrastructure\Mapper\Attribute\MapFrom;

#[MapFrom(UpdateApplicationManagerRequest::class)]
final class UpdateApplicationManagerCommandMapper
{
    #[MapField]
    public string $id;

    #[MapField]
    public ?ApplicationName $name = null;

    #[MapField('request_url')]
    public ?RequestUrl $requestUrl = null;

    #[MapField('is_active')]
    public ?bool $isActive = null;

    #[MapField('ip_whitelist')]
    public ?IpWhitelist $ipWhitelist = null;

    public function toCommand(): UpdateApplicationManagerCommand
    {
        return new UpdateApplicationManagerCommand(
            id: Uuid::fromString($this->id),
            name: $this->name,
            requestUrl: $this->requestUrl,
            isActive: $this->isActive,
            ipWhitelist: $this->ipWhitelist,
        );
    }
}
