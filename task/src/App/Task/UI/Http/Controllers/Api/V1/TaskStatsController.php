<?php

declare(strict_types=1);

namespace App\Task\UI\Http\Controllers\Api\V1;

use App\Shared\UI\Http\Controllers\Api\ApiController;
use App\Task\Application\Query\ListTasks\CountStatusesTaskHandler;
use App\Task\UI\Http\Mappers\CountStatsTaskQueryMapper;
use App\Shared\Infrastructure\Mapper\GenericRequestMapper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

final class TaskStatsController extends ApiController
{

    public function __construct(
        private readonly CountStatusesTaskHandler $countStatusesTaskHandler,
        private readonly GenericRequestMapper $mapper,
    ){}

    #[OA\Get(
        path: '/v1/tasks/stats/status',
        summary: 'Ilość zadań wg statusu',
        tags: ['Tasks'],
        security: [['jwt' => []]],
        parameters: [
            new OA\Parameter(name: 'site', in: 'query', description: 'Site', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'status', in: 'query', description: 'Status', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'application_manager_id', in: 'query', description: 'Application Manager ID', schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Ilość zadań'),
        ]
    )]
    public function statusesCount(Request $request): JsonResponse
    {
        /** @var CountStatsTaskQueryMapper $mapperDto */
        $mapperDto = $this->mapper->map($request, CountStatsTaskQueryMapper::class);

        $result = $this->countStatusesTaskHandler->handle($mapperDto->toDto());

        return $this->success($result->toArray());
    }
}
