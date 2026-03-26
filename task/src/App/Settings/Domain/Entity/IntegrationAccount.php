<?php

declare(strict_types=1);

namespace App\Settings\Domain\Entity;

use App\Shared\Domain\ValueObject\Uuid;
use DateTimeImmutable;

final class IntegrationAccount
{
    /**
     * @param array<string, mixed> $credentials
     */
    private function __construct(
        private Uuid $id,
        private string $name,
        private bool $enabled,
        private ?string $externalAccountId,
        private ?string $provider,
        private array $credentials,
        private ?DateTimeImmutable $createdAt,
        private ?DateTimeImmutable $updatedAt,
    ) {
    }

    /**
     * @param array<string, mixed> $credentials
     */
    public static function create(
        string $name,
        bool $enabled,
        ?string $externalAccountId,
        ?string $provider,
        array $credentials,
    ): self {
        return new self(
            Uuid::generate(),
            $name,
            $enabled,
            $externalAccountId,
            $provider,
            $credentials,
            null,
            null,
        );
    }

    /**
     * @param array<string, mixed> $credentials
     */
    public static function reconstitute(
        Uuid $id,
        string $name,
        bool $enabled,
        ?string $externalAccountId,
        ?string $provider,
        array $credentials,
        ?DateTimeImmutable $createdAt,
        ?DateTimeImmutable $updatedAt,
    ): self {
        return new self($id, $name, $enabled, $externalAccountId, $provider, $credentials, $createdAt, $updatedAt);
    }

    /**
     * @param array<string, mixed> $credentials
     */
    public function update(
        string $name,
        bool $enabled,
        ?string $externalAccountId,
        ?string $provider,
        array $credentials,
    ): void {
        $this->name = $name;
        $this->enabled = $enabled;
        $this->externalAccountId = $externalAccountId;
        $this->provider = $provider;
        $this->credentials = $credentials;
    }

    public function enable(): void
    {
        $this->enabled = true;
    }

    public function disable(): void
    {
        $this->enabled = false;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function getExternalAccountId(): ?string
    {
        return $this->externalAccountId;
    }

    public function getProvider(): ?string
    {
        return $this->provider;
    }

    /**
     * @return array<string, mixed>
     */
    public function getCredentials(): array
    {
        return $this->credentials;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
