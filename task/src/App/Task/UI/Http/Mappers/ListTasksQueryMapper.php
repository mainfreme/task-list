<?php

declare(strict_types=1);

namespace App\Task\UI\Http\Mappers;

use App\Shared\Domain\ValueObject\Uuid;
use App\Shared\Infrastructure\Mapper\Attribute\MapField;
use App\Shared\Infrastructure\Mapper\Attribute\MapFrom;
use App\Task\Application\Query\ListTasks\ListTasksQuery;
use App\Task\Domain\ValueObject\ApplicationManagerId;
use Illuminate\Http\Request;

#[MapFrom(Request::class)]
final class ListTasksQueryMapper
{
    #[MapField]
    public int $page = 1;

    #[MapField('per_page')]
    public int $perPage = 20;

    #[MapField]
    public ?string $status = null;

    #[MapField('application_manager_id')]
    public ?ApplicationManagerId $applicationManagerId = null;

    /** CSV lub pojedynczy UUID z query (np. `application_manager_ids=id1,id2`) */
    #[MapField('application_manager_ids')]
    public ?string $applicationManagerIdsCsv = null;

    /** CSV UUID użytkowników (np. `user_ids=id1,id2`) */
    #[MapField('user_ids')]
    public ?string $userIdsCsv = null;

    #[MapField('sort_by')]
    public string $sortBy = 'created_at';

    #[MapField('sort_dir')]
    public string $sortDir = 'desc';

    public function toQuery(): ListTasksQuery
    {
        $sortBy = in_array($this->sortBy, ['title', 'created_at', 'status'], true) ? $this->sortBy : 'created_at';
        $sortDir = strtolower($this->sortDir) === 'asc' ? 'asc' : 'desc';

        $ids = $this->parseApplicationManagerIds();
        $userIds = $this->parseUserIds();

        return new ListTasksQuery(
            page: $this->page,
            perPage: $this->perPage,
            status: $this->status,
            applicationManagerIds: $ids,
            userIds: $userIds,
            sortBy: $sortBy,
            sortDir: $sortDir,
        );
    }

    /**
     * @return list<ApplicationManagerId>
     */
    private function parseApplicationManagerIds(): array
    {
        $raw = [];
        if ($this->applicationManagerIdsCsv !== null && $this->applicationManagerIdsCsv !== '') {
            $raw = array_map('trim', explode(',', $this->applicationManagerIdsCsv));
        }
        if ($this->applicationManagerId !== null && $raw === []) {
            return [$this->applicationManagerId];
        }
        $out = [];
        foreach ($raw as $s) {
            if ($s === '') {
                continue;
            }
            $out[] = ApplicationManagerId::fromString($s);
        }

        return $out;
    }

    /**
     * @return list<Uuid>
     */
    private function parseUserIds(): array
    {
        if ($this->userIdsCsv === null || $this->userIdsCsv === '') {
            return [];
        }
        $raw = array_map('trim', explode(',', $this->userIdsCsv));
        $out = [];
        foreach ($raw as $s) {
            if ($s === '') {
                continue;
            }
            $out[] = Uuid::fromString($s);
        }

        return $out;
    }
}
