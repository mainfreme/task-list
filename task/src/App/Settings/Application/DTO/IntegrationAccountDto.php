<?php

declare(strict_types=1);

namespace App\Settings\Application\DTO;

final class IntegrationAccountDto
{
    /**
     * @param array<string, mixed> $credentials
     */
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly bool $enabled,
        public readonly ?string $externalAccountId,
        public readonly ?string $provider,
        public readonly array $credentials,
        public readonly ?string $createdAt,
        public readonly ?string $updatedAt,
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'enabled' => $this->enabled,
            'external_account_id' => $this->externalAccountId,
            'provider' => $this->provider,
            'credentials' => $this->credentials,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
