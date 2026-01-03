<?php

declare(strict_types=1);

namespace App\Application\ApplicationManager\DTO;

final class ApplicationManagerDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly ?string $apiKey = null, // Only returned when creating/regenerating
        public readonly ?string $requestUrl = null,
        public readonly bool $isActive = true,
        public readonly ?array $ipWhitelist = null,
        public readonly string $createdAt,
        public readonly string $updatedAt,
    ) {
    }

    public function toArray(): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'request_url' => $this->requestUrl,
            'is_active' => $this->isActive,
            'ip_whitelist' => $this->ipWhitelist,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];

        if ($this->apiKey !== null) {
            $data['api_key'] = $this->apiKey;
        }

        return $data;
    }
}

