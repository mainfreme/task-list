<?php

declare(strict_types=1);

namespace App\Task\Application\Query\ListTasks;

use App\Task\Domain\ValueObject\ApplicationManagerId;
use App\Shared\Infrastructure\Mapper\Attribute\MapFrom;
use App\Shared\Infrastructure\Mapper\Attribute\MapField;
use App\Shared\Infrastructure\Mapper\Source;

#[MapFrom(Source::QUERY)]
final class ListTasksQuery
{
    public function __construct(
        #[MapField('page')]
        public readonly int $page = 1,
        #[MapField('per_page')]
        public readonly int $perPage = 20,
        #[MapField('status')]
        public readonly ?string $status = null,
        #[MapField('application_manager_id')]
        public readonly ?ApplicationManagerId $applicationManagerId = null,
    ) {
    }
}
