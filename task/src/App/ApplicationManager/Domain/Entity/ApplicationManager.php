<?php

declare(strict_types=1);

namespace App\ApplicationManager\Domain\Entity;

use App\ApplicationManager\Domain\ValueObject\ApiKey;
use App\ApplicationManager\Domain\ValueObject\ApplicationName;
use App\ApplicationManager\Domain\ValueObject\IpWhitelist;
use App\ApplicationManager\Domain\ValueObject\RequestUrl;
use App\Shared\Domain\ValueObject\Uuid;

final class ApplicationManager
{
    private ?Uuid $id = null;

    public function __construct(
        private ApplicationName $name,
        private ApiKey $apiKey,
        private ?RequestUrl $requestUrl = null,
        private bool $isActive = true,
        private ?IpWhitelist $ipWhitelist = null,
        private ?\DateTimeImmutable $createdAt = null,
        private ?\DateTimeImmutable $updatedAt = null
    ) {
        $this->createdAt = $this->createdAt ?? new \DateTimeImmutable();
        $this->updatedAt = $this->updatedAt ?? new \DateTimeImmutable();
    }

    public static function create(
        ApplicationName $name,
        ApiKey $apiKey,
        ?RequestUrl $requestUrl = null,
        bool $isActive = true,
        ?IpWhitelist $ipWhitelist = null
    ): self {
        return new self($name, $apiKey, $requestUrl, $isActive, $ipWhitelist);
    }

    public static function fromDatabase(
        ApplicationName $name,
        ApiKey $apiKey,
        ?RequestUrl $requestUrl = null,
        bool $isActive = true,
        ?IpWhitelist $ipWhitelist = null,
        ?\DateTimeImmutable $createdAt = null,
        ?\DateTimeImmutable $updatedAt = null
    ): self {
        return new self($name, $apiKey, $requestUrl, $isActive, $ipWhitelist, $createdAt, $updatedAt);
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function setId(Uuid $id): void
    {
        $this->id = $id;
    }

    public function getName(): ApplicationName
    {
        return $this->name;
    }

    public function setName(ApplicationName $name): void
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

    public function getRequestUrl(): ?RequestUrl
    {
        return $this->requestUrl;
    }

    public function setRequestUrl(?RequestUrl $requestUrl): void
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

    public function getIpWhitelist(): ?IpWhitelist
    {
        return $this->ipWhitelist;
    }

    public function setIpWhitelist(?IpWhitelist $ipWhitelist): void
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
