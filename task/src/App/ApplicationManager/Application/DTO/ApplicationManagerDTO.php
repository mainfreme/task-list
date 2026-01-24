<?php

declare(strict_types=1);

namespace App\ApplicationManager\Application\DTO;

use App\ApplicationManager\Domain\ValueObject\Uuid;
use App\ApplicationManager\Domain\ValueObject\ApplicationName;
use App\ApplicationManager\Domain\ValueObject\ApiKey;
use App\ApplicationManager\Domain\ValueObject\RequestUrl;
use App\ApplicationManager\Domain\ValueObject\IpWhitelist;

final class ApplicationManagerDTO
{
    public function __construct(
        public readonly Uuid $id,
        public readonly ApplicationName $name,
        public readonly ?ApiKey $apiKey = null, // Only returned when creating/regenerating
        public readonly ?RequestUrl $requestUrl = null,
        public readonly bool $isActive = true,
        public readonly ?IpWhitelist $ipWhitelist = null,
    ) {
    }

    public function toArray(): array
    {
        $data = [
            'id' => $this->id->getValue(),
            'name' => $this->name->getValue(),
            'request_url' => $this->requestUrl?->getValue(),
            'is_active' => $this->isActive,
            'ip_whitelist' => $this->ipWhitelist?->toArray()
        ];

        if ($this->apiKey !== null) {
            $data['api_key'] = $this->apiKey->value();
        }

        return $data;
    }
}
