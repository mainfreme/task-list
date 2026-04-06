<?php

declare(strict_types=1);

namespace App\ApplicationManager\Infrastructure\Mapper;

use App\ApplicationManager\Domain\Entity\ApplicationManager;
use App\ApplicationManager\Domain\ValueObject\ApiKey;
use App\ApplicationManager\Domain\ValueObject\ApplicationName;
use App\ApplicationManager\Domain\ValueObject\IpWhitelist;
use App\ApplicationManager\Domain\ValueObject\RequestUrl;
use App\ApplicationManager\Infrastructure\Eloquent\ApplicationManagerModel;
use App\Shared\Domain\ValueObject\Uuid;
use JsonException;

final class ApplicationManagerEntityMapper
{
    /**
     * @param array<string, mixed> $payload
     */
    public static function fromCachePayload(array $payload): ApplicationManager
    {
        $createdAt = isset($payload['created_at']) && is_string($payload['created_at'])
            ? self::parseDateTime($payload['created_at'])
            : new \DateTimeImmutable();
        $updatedAt = isset($payload['updated_at']) && is_string($payload['updated_at'])
            ? self::parseDateTime($payload['updated_at'])
            : new \DateTimeImmutable();

        /** @var array<string>|null $ipWhitelistRaw */
        $ipWhitelistRaw = $payload['ip_whitelist'] ?? null;
        $ipWhitelist = $ipWhitelistRaw === null ? null : IpWhitelist::fromNullable($ipWhitelistRaw);

        $entity = ApplicationManager::fromDatabase(
            ApplicationName::fromString((string) $payload['name']),
            ApiKey::generate(),
            RequestUrl::fromNullable(isset($payload['request_url']) ? (string) $payload['request_url'] : null),
            (bool) ($payload['is_active'] ?? true),
            $ipWhitelist,
            $createdAt,
            $updatedAt
        );
        $entity->setId(Uuid::fromString((string) $payload['id']));

        return $entity;
    }

    public static function fromModel(ApplicationManagerModel $model): ApplicationManager
    {
        $apiKey = ApiKey::generate();

        $entity = ApplicationManager::fromDatabase(
            ApplicationName::fromString($model->name),
            $apiKey,
            RequestUrl::fromNullable($model->request_url),
            $model->is_active,
            IpWhitelist::fromNullable($model->ip_whitelist),
            $model->created_at ? \DateTimeImmutable::createFromMutable($model->created_at) : null,
            $model->updated_at ? \DateTimeImmutable::createFromMutable($model->updated_at) : null
        );

        $entity->setId(Uuid::fromString($model->id));

        return $entity;
    }

    /**
     * @throws JsonException
     */
    public static function entityToJson(ApplicationManager $entity, int $valueVersion): string
    {
        $id = $entity->getId();
        if ($id === null) {
            throw new \InvalidArgumentException('ApplicationManager must have an id to be cached');
        }

        $payload = [
            'v' => $valueVersion,
            'id' => $id->getValue(),
            'name' => $entity->getName()->getValue(),
            'request_url' => $entity->getRequestUrl()?->getValue(),
            'is_active' => $entity->isActive(),
            'ip_whitelist' => $entity->getIpWhitelist()?->toArray(),
            'created_at' => $entity->getCreatedAt()->format(\DateTimeInterface::ATOM),
            'updated_at' => $entity->getUpdatedAt()->format(\DateTimeInterface::ATOM),
        ];

        return json_encode($payload, JSON_THROW_ON_ERROR);
    }

    /**
     * @throws JsonException
     */
    public static function jsonToEntity(string $json, int $expectedVersion): ?ApplicationManager
    {
        /** @var array<string, mixed> $payload */
        $payload = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        if (!isset($payload['v']) || (int) $payload['v'] !== $expectedVersion) {
            return null;
        }

        if (!isset($payload['id'], $payload['name'])) {
            return null;
        }

        return self::fromCachePayload($payload);
    }

    private static function parseDateTime(string $value): \DateTimeImmutable
    {
        $dt = \DateTimeImmutable::createFromFormat(\DateTimeInterface::ATOM, $value);

        return $dt !== false ? $dt : new \DateTimeImmutable($value);
    }
}
