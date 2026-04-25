<?php

declare(strict_types=1);

namespace App\Event\Application\DTO;

use App\Event\Domain\Entity\SystemEvent;

final class SystemEventDTO
{
    /**
     * @param array<string, mixed>|null $changes
     * @param array<string, mixed>|null $metadata
     */
    public function __construct(
        public readonly string $id,
        public readonly string $userId,
        public readonly ?string $action,
        public readonly ?string $label,
        public readonly ?string $message,
        public readonly ?array $changes,
        public readonly ?string $url,
        public readonly ?string $ipAddress,
        public readonly ?array $metadata,
        public readonly ?string $module,
        public readonly ?string $applicationId,
        public readonly ?string $createdAt,
    ) {
    }

    public static function fromEntity(SystemEvent $event): self
    {
        $metadata = $event->getMetadata();
        $module = is_array($metadata) && isset($metadata['module']) ? (string) $metadata['module'] : null;
        $applicationId = is_array($metadata) && isset($metadata['application_id'])
            ? (string) $metadata['application_id']
            : null;

        return new self(
            id: $event->getId()->getValue(),
            userId: $event->getUserId()->getValue(),
            action: $event->getAction(),
            label: $event->getLabel(),
            message: $event->getMessage(),
            changes: $event->getChanges(),
            url: $event->getUrl(),
            ipAddress: $event->getIpAddress(),
            metadata: $metadata,
            module: $module,
            applicationId: $applicationId,
            createdAt: $event->getCreatedAt()?->format(DATE_ATOM),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'action' => $this->action,
            'label' => $this->label,
            'message' => $this->message,
            'changes' => $this->changes,
            'url' => $this->url,
            'ip_address' => $this->ipAddress,
            'metadata' => $this->metadata,
            'module' => $this->module,
            'application_id' => $this->applicationId,
            'created_at' => $this->createdAt,
        ];
    }
}
