<?php

declare(strict_types=1);

namespace App\Settings\Application\DTO;

final class SettingEntryDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $groupKey,
        public readonly string $fieldKey,
        public readonly string $fieldType,
        public readonly ?string $value,
        public readonly ?string $createdAt,
        public readonly ?string $updatedAt,
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'group_key' => $this->groupKey,
            'field_key' => $this->fieldKey,
            'field_type' => $this->fieldType,
            'value' => $this->value,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (string) ($data['id'] ?? ''),
            groupKey: (string) ($data['group_key'] ?? ''),
            fieldKey: (string) ($data['field_key'] ?? ''),
            fieldType: (string) ($data['field_type'] ?? ''),
            value: isset($data['value']) ? (string) $data['value'] : null,
            createdAt: isset($data['created_at']) ? (string) $data['created_at'] : null,
            updatedAt: isset($data['updated_at']) ? (string) $data['updated_at'] : null,
        );
    }
}
