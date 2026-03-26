<?php

declare(strict_types=1);

namespace App\Settings\Infrastructure\Repository;

use App\Settings\Domain\Entity\ChartDefinition;
use App\Settings\Domain\Exception\ChartDefinitionNotFoundException;
use App\Settings\Domain\Repository\ChartDefinitionRepositoryInterface;
use App\Settings\Infrastructure\Model\ChartDefinitionModel;
use App\Shared\Domain\ValueObject\Uuid;
use DateTimeImmutable;

final class EloquentChartDefinitionRepository implements ChartDefinitionRepositoryInterface
{
    public function findById(Uuid $id): ChartDefinition
    {
        $model = ChartDefinitionModel::find($id->getValue());

        if (!$model) {
            throw ChartDefinitionNotFoundException::byId($id->getValue());
        }

        return $this->toEntity($model);
    }

    public function findAll(): array
    {
        return ChartDefinitionModel::orderBy('created_at', 'desc')
            ->get()
            ->map(fn (ChartDefinitionModel $m) => $this->toEntity($m))
            ->all();
    }

    public function save(ChartDefinition $definition): void
    {
        $data = [
            'id' => $definition->getId()->getValue(),
            'chart_type' => $definition->getChartType(),
            'display_fields' => $definition->getDisplayFields(),
            'sql_query' => $definition->getSqlQuery(),
        ];

        $exists = ChartDefinitionModel::where('id', $definition->getId()->getValue())->exists();

        if (!$exists) {
            ChartDefinitionModel::create($data);
        } else {
            unset($data['id']);
            ChartDefinitionModel::where('id', $definition->getId()->getValue())->update($data);
        }
    }

    public function delete(ChartDefinition $definition): void
    {
        $model = ChartDefinitionModel::find($definition->getId()->getValue());

        if (!$model) {
            throw ChartDefinitionNotFoundException::byId($definition->getId()->getValue());
        }

        $model->delete();
    }

    private function toEntity(ChartDefinitionModel $model): ChartDefinition
    {
        /** @var array<int|string, mixed> $displayFields */
        $displayFields = $model->display_fields ?? [];

        return ChartDefinition::reconstitute(
            id: Uuid::fromString($model->id),
            chartType: $model->chart_type,
            displayFields: $displayFields,
            sqlQuery: $model->sql_query,
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
