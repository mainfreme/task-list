<?php

declare(strict_types=1);

namespace App\Event\Infrastructure\Repository;

use App\Event\Domain\Entity\SystemEvent;
use App\Event\Domain\Repository\SystemEventRepositoryInterface;
use App\Event\Infrastructure\Model\SystemEventModel;
use App\Shared\Domain\ValueObject\Uuid;
use DateTimeImmutable;
use Illuminate\Database\Eloquent\Builder;

final class EloquentSystemEventRepository implements SystemEventRepositoryInterface
{
    public function findForList(
        array $userIds,
        array $applicationIds,
        array $modules,
        ?DateTimeImmutable $dateFrom,
        ?DateTimeImmutable $dateTo,
        int $limit,
        int $offset,
        string $sortDir,
    ): array {
        $dir = strtolower($sortDir) === 'asc' ? 'asc' : 'desc';

        $models = $this->baseQuery($userIds, $applicationIds, $modules, $dateFrom, $dateTo)
            ->orderBy('created_at', $dir)
            ->orderBy('id', $dir)
            ->limit($limit)
            ->offset($offset)
            ->get();

        $out = [];
        foreach ($models as $model) {
            $out[] = $this->toEntity($model);
        }

        return $out;
    }

    public function countForList(
        array $userIds,
        array $applicationIds,
        array $modules,
        ?DateTimeImmutable $dateFrom,
        ?DateTimeImmutable $dateTo,
    ): int {
        return $this->baseQuery($userIds, $applicationIds, $modules, $dateFrom, $dateTo)->count();
    }

    public function distinctModules(): array
    {
        $rows = SystemEventModel::query()
            ->selectRaw("DISTINCT metadata->>'module' AS module")
            ->whereNotNull('metadata')
            ->whereRaw("metadata->>'module' IS NOT NULL")
            ->orderByRaw("metadata->>'module' ASC")
            ->pluck('module');

        $out = [];
        foreach ($rows as $module) {
            if (is_string($module) && $module !== '') {
                $out[] = $module;
            }
        }

        return $out;
    }

    /**
     * @param list<Uuid>    $userIds
     * @param list<string>  $applicationIds
     * @param list<string>  $modules
     */
    private function baseQuery(
        array $userIds,
        array $applicationIds,
        array $modules,
        ?DateTimeImmutable $dateFrom,
        ?DateTimeImmutable $dateTo,
    ): Builder {
        $userIdValues = array_map(static fn (Uuid $id) => $id->getValue(), $userIds);

        return SystemEventModel::query()
            ->when($userIdValues !== [], fn (Builder $q) => $q->whereIn('user_id', $userIdValues))
            ->when(
                $applicationIds !== [],
                fn (Builder $q) => $q->whereIn('metadata->application_id', $applicationIds),
            )
            ->when(
                $modules !== [],
                fn (Builder $q) => $q->whereIn('metadata->module', $modules),
            )
            ->when($dateFrom !== null, fn (Builder $q) => $q->where('created_at', '>=', $dateFrom->format('Y-m-d H:i:s')))
            ->when($dateTo !== null, fn (Builder $q) => $q->where('created_at', '<=', $dateTo->format('Y-m-d H:i:s')));
    }

    private function toEntity(SystemEventModel $model): SystemEvent
    {
        return SystemEvent::reconstitute(
            Uuid::fromString($model->id),
            Uuid::fromString($model->user_id),
            $model->action,
            $model->label,
            $model->message,
            is_array($model->changes) ? $model->changes : null,
            $model->url,
            $model->ip_address,
            is_array($model->metadata) ? $model->metadata : null,
            $model->created_at ? DateTimeImmutable::createFromMutable($model->created_at) : null,
            $model->updated_at ? DateTimeImmutable::createFromMutable($model->updated_at) : null,
            $model->deleted_at ? DateTimeImmutable::createFromMutable($model->deleted_at) : null,
        );
    }
}
