<?php

declare(strict_types=1);

namespace App\Task\UI\Http\Mappers;

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

    #[MapField('sort_by')]
    public string $sortBy = 'created_at';

    #[MapField('sort_dir')]
    public string $sortDir = 'desc';

    public function toQuery(): ListTasksQuery
    {
        $sortBy = in_array($this->sortBy, ['title', 'created_at', 'status'], true) ? $this->sortBy : 'created_at';
        $sortDir = strtolower($this->sortDir) === 'asc' ? 'asc' : 'desc';

        return new ListTasksQuery(
            page: $this->page,
            perPage: $this->perPage,
            status: $this->status,
            applicationManagerId: $this->applicationManagerId,
            sortBy: $sortBy,
            sortDir: $sortDir,
        );
    }
}
