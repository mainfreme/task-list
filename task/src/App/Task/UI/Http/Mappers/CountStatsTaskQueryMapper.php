<?php

declare(strict_types=1);

namespace App\Task\UI\Http\Mappers;

use App\Shared\Infrastructure\Mapper\Attribute\MapField;
use App\Shared\Infrastructure\Mapper\Attribute\MapFrom;
use App\Task\Domain\DTO\Stats\CountStatusesTaskDto;
use App\Task\Domain\ValueObject\ApplicationManagerId;
use Illuminate\Http\Request;

#[MapFrom(Request::class)]
final class CountStatsTaskQueryMapper
{
    #[MapField]
    public ?string $site = null;

    #[MapField]
    public ?string $status = null;

    #[MapField]
    public ?ApplicationManagerId $applicationManagerId = null;

    public function toDto(): CountStatusesTaskDto
    {
        return new CountStatusesTaskDto(
            site: $this->site,
            status: $this->status,
            applicationManagerId: $this->applicationManagerId,
        );
    }
}
