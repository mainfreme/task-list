<?php

declare(strict_types=1);

namespace App\Settings\Application\DTO;

final class IntegrationAccountSummaryDto
{
    /**
     * @param array<string, mixed> $credentialsMasked
     */
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly bool $enabled,
        public readonly ?string $externalAccountId,
        public readonly ?string $provider,
        public readonly array $credentialsMasked,
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
            'credentials' => $this->credentialsMasked,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $credentialsMasked = $data['credentials'] ?? [];

        return new self(
            id: (string) ($data['id'] ?? ''),
            name: (string) ($data['name'] ?? ''),
            enabled: (bool) ($data['enabled'] ?? false),
            externalAccountId: isset($data['external_account_id']) ? (string) $data['external_account_id'] : null,
            provider: isset($data['provider']) ? (string) $data['provider'] : null,
            credentialsMasked: is_array($credentialsMasked) ? $credentialsMasked : [],
            createdAt: isset($data['created_at']) ? (string) $data['created_at'] : null,
            updatedAt: isset($data['updated_at']) ? (string) $data['updated_at'] : null,
        );
    }
}
