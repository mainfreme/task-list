<?php

declare(strict_types=1);

namespace App\Settings\Domain\Event;

final readonly class SettingsChangedEvent
{
    public const OPERATION_CREATED = 'created';
    public const OPERATION_UPDATED = 'updated';
    public const OPERATION_DELETED = 'deleted';

    /**
     * @param array<string, mixed>|null $before
     * @param array<string, mixed>|null $after
     * @param array<int, string> $changedFields
     */
    public function __construct(
        public string $resourceType,
        public string $resourceId,
        public string $operation,
        public ?array $before,
        public ?array $after,
        public array $changedFields = [],
        public ?string $actorId = null,
        public ?string $requestUrl = null,
        public ?string $ipAddress = null,
        public ?string $userAgent = null,
    ) {
    }
}
