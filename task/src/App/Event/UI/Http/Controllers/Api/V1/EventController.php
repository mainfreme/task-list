<?php

declare(strict_types=1);

namespace App\Event\UI\Http\Controllers\Api\V1;

use App\Event\Application\Query\ListEventFilters\ListEventFiltersHandler;
use App\Event\Application\Query\ListEvents\ListEventsHandler;
use App\Event\UI\Http\Mappers\ListEventsQueryMapper;
use App\Shared\Infrastructure\Mapper\GenericRequestMapper;
use App\Shared\UI\Http\Controllers\Api\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

final class EventController extends ApiController
{
    public function __construct(
        private readonly ListEventsHandler $listEventsHandler,
        private readonly ListEventFiltersHandler $listEventFiltersHandler,
        private readonly GenericRequestMapper $mapper,
    ) {
    }

    #[OA\Get(
        path: '/v1/events',
        summary: 'List system events (actions) with filters',
        tags: ['Events'],
        security: [['jwt' => []]],
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\Parameter(name: 'per_page', in: 'query', schema: new OA\Schema(type: 'integer', default: 20)),
            new OA\Parameter(name: 'user_ids', in: 'query', description: 'CSV UUID użytkowników', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'application_ids', in: 'query', description: 'CSV identyfikatorów aplikacji (metadata.application_id)', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'modules', in: 'query', description: 'CSV nazw modułów (metadata.module)', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'date_from', in: 'query', schema: new OA\Schema(type: 'string', format: 'date-time')),
            new OA\Parameter(name: 'date_to', in: 'query', schema: new OA\Schema(type: 'string', format: 'date-time')),
            new OA\Parameter(name: 'sort_dir', in: 'query', schema: new OA\Schema(type: 'string', enum: ['asc', 'desc'], default: 'desc')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Stronicowana lista zdarzeń'),
        ],
    )]
    public function index(Request $request): JsonResponse
    {
        /** @var ListEventsQueryMapper $queryMapper */
        $queryMapper = $this->mapper->map($request, ListEventsQueryMapper::class);
        $query = $queryMapper->toQuery();

        $result = $this->listEventsHandler->handle($query);

        return $this->success($result->toArray());
    }

    #[OA\Get(
        path: '/v1/events/filters',
        summary: 'Get distinct values for event filters (modules, ...)',
        tags: ['Events'],
        security: [['jwt' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Dostępne wartości filtrów'),
        ],
    )]
    public function filters(): JsonResponse
    {
        return $this->success($this->listEventFiltersHandler->handle());
    }
}
