<?php

declare(strict_types=1);

namespace App\Settings\Infrastructure\Repository;

use App\Settings\Domain\Entity\SettingEntry;
use App\Settings\Domain\Exception\SettingEntryNotFoundException;
use App\Settings\Domain\Repository\SettingEntryRepositoryInterface;
use App\Settings\Domain\ValueObject\SettingFieldType;
use App\Settings\Infrastructure\Model\SettingEntryModel;
use App\Shared\Domain\ValueObject\Uuid;
use DateTimeImmutable;

final class EloquentSettingEntryRepository implements SettingEntryRepositoryInterface
{
    public function findById(Uuid $id): SettingEntry
    {
        $model = SettingEntryModel::find($id->getValue());

        if (!$model) {
            throw SettingEntryNotFoundException::byId($id->getValue());
        }

        return $this->toEntity($model);
    }

    public function findByGroupAndField(string $groupKey, string $fieldKey): ?SettingEntry
    {
        $model = SettingEntryModel::query()
            ->where('group_key', $groupKey)
            ->where('field_key', $fieldKey)
            ->first();

        if (!$model) {
            return null;
        }

        return $this->toEntity($model);
    }

    public function findByGroup(string $groupKey): array
    {
        return SettingEntryModel::query()
            ->where('group_key', $groupKey)
            ->orderBy('field_key')
            ->get()
            ->map(fn (SettingEntryModel $m) => $this->toEntity($m))
            ->all();
    }

    public function findAllGroupedByGroupKey(): array
    {
        $models = SettingEntryModel::query()
            ->orderBy('group_key')
            ->orderBy('field_key')
            ->get();

        /** @var array<string, SettingEntry[]> $grouped */
        $grouped = [];
        foreach ($models as $model) {
            $key = $model->group_key;
            if (!isset($grouped[$key])) {
                $grouped[$key] = [];
            }
            $grouped[$key][] = $this->toEntity($model);
        }

        return $grouped;
    }

    public function save(SettingEntry $entry): void
    {
        $data = [
            'id' => $entry->getId()->getValue(),
            'group_key' => $entry->getGroupKey(),
            'field_key' => $entry->getFieldKey(),
            'field_type' => $entry->getFieldType()->value,
            'value' => $entry->getValue(),
        ];

        $exists = SettingEntryModel::where('id', $entry->getId()->getValue())->exists();

        if (!$exists) {
            SettingEntryModel::create($data);
        } else {
            unset($data['id']);
            SettingEntryModel::where('id', $entry->getId()->getValue())->update($data);
        }
    }

    public function delete(SettingEntry $entry): void
    {
        $model = SettingEntryModel::find($entry->getId()->getValue());

        if (!$model) {
            throw SettingEntryNotFoundException::byId($entry->getId()->getValue());
        }

        $model->delete();
    }

    private function toEntity(SettingEntryModel $model): SettingEntry
    {
        $fieldType = SettingFieldType::from($model->field_type);

        return SettingEntry::reconstitute(
            id: Uuid::fromString($model->id),
            groupKey: $model->group_key,
            fieldKey: $model->field_key,
            fieldType: $fieldType,
            value: $model->value,
            createdAt: $this->toImmutable($model->created_at),
            updatedAt: $this->toImmutable($model->updated_at),
        );
    }

    private function toImmutable(mixed $value): ?DateTimeImmutable
    {
        if ($value === null) {
            return null;
        }

        return DateTimeImmutable::createFromMutable($value);
    }
}
