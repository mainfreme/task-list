<?php

declare(strict_types=1);

namespace App\ApplicationManager\Domain\Entity;

use App\ApplicationManager\Domain\ValueObject\ApiKey;

final class ApplicationManager
{
    private ?int $id = null;

    public function __construct(
        private string $name,
        private ApiKey $apiKey,
        private ?string $requestUrl = null,
        private bool $isActive = true,
        private ?array $ipWhitelist = null,
        private ?\DateTimeImmutable $createdAt = null,
        private ?\DateTimeImmutable $updatedAt = null
    ) {
        $this->createdAt = $this->createdAt ?? new \DateTimeImmutable();
        $this->updatedAt = $this->updatedAt ?? new \DateTimeImmutable();
    }

    public static function create(
        string $name,
        ApiKey $apiKey,
        ?string $requestUrl = null,
        bool $isActive = true,
        ?array $ipWhitelist = null
    ): self {
        return new self($name, $apiKey, $requestUrl, $isActive, $ipWhitelist);
    }

    public static function fromDatabase(
        string $name,
        ApiKey $apiKey,
        ?string $requestUrl = null,
        bool $isActive = true,
        ?array $ipWhitelist = null,
        ?\DateTimeImmutable $createdAt = null,
        ?\DateTimeImmutable $updatedAt = null
    ): self {
        return new self($name, $apiKey, $requestUrl, $isActive, $ipWhitelist, $createdAt, $updatedAt);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
        $this->touch();
    }

    public function getApiKey(): ApiKey
    {
        return $this->apiKey;
    }

    public function setApiKey(ApiKey $apiKey): void
    {
        $this->apiKey = $apiKey;
        $this->touch();
    }

    public function getRequestUrl(): ?string
    {
        return $this->requestUrl;
    }

    public function setRequestUrl(?string $requestUrl): void
    {
        $this->requestUrl = $requestUrl;
        $this->touch();
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function activate(): void
    {
        $this->isActive = true;
        $this->touch();
    }

    public function deactivate(): void
    {
        $this->isActive = false;
        $this->touch();
    }

    public function getIpWhitelist(): ?array
    {
        return $this->ipWhitelist;
    }

    public function setIpWhitelist(?array $ipWhitelist): void
    {
        $this->ipWhitelist = $ipWhitelist;
        $this->touch();
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    private function touch(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
}
