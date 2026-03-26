<?php

declare(strict_types=1);

namespace App\Settings\Domain\Entity;

use App\Settings\Domain\ValueObject\SettingFieldType;
use App\Shared\Domain\ValueObject\Uuid;
use DateTimeImmutable;

final class SettingEntry
{
    private function __construct(
        private Uuid $id,
        private string $groupKey,
        private string $fieldKey,
        private SettingFieldType $fieldType,
        private ?string $value,
        private ?DateTimeImmutable $createdAt,
        private ?DateTimeImmutable $updatedAt,
    ) {
    }

    public static function create(
        string $groupKey,
        string $fieldKey,
        SettingFieldType $fieldType,
        ?string $value,
    ): self {
        return new self(
            Uuid::generate(),
            $groupKey,
            $fieldKey,
            $fieldType,
            $value,
            null,
            null,
        );
    }

    public static function reconstitute(
        Uuid $id,
        string $groupKey,
        string $fieldKey,
        SettingFieldType $fieldType,
        ?string $value,
        ?DateTimeImmutable $createdAt,
        ?DateTimeImmutable $updatedAt,
    ): self {
        return new self($id, $groupKey, $fieldKey, $fieldType, $value, $createdAt, $updatedAt);
    }

    public function update(SettingFieldType $fieldType, ?string $value): void
    {
        $this->fieldType = $fieldType;
        $this->value = $value;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getGroupKey(): string
    {
        return $this->groupKey;
    }

    public function getFieldKey(): string
    {
        return $this->fieldKey;
    }

    public function getFieldType(): SettingFieldType
    {
        return $this->fieldType;
    }

    public function getValue(): ?string
    {
        return $this->value;
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
