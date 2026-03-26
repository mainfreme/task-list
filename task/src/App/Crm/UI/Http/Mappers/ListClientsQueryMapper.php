<?php

declare(strict_types=1);

namespace App\Crm\UI\Http\Mappers;

use App\Crm\Application\Query\ListClients\ListClientsQuery;
use App\Crm\Domain\Enums\ClientStatus;
use App\Shared\Infrastructure\Mapper\Attribute\MapField;
use App\Shared\Infrastructure\Mapper\Attribute\MapFrom;
use Illuminate\Http\Request;

#[MapFrom(Request::class)]
final class ListClientsQueryMapper
{
    #[MapField]
    public int $page = 1;

    #[MapField('per_page')]
    public int $perPage = 20;

    #[MapField]
    public ?ClientStatus $status = null;

    public function toQuery(): ListClientsQuery
    {
        return new ListClientsQuery(
            page: (int) $this->page,
            perPage: (int) $this->perPage,
            status: $this->status,
        );
    }
}
