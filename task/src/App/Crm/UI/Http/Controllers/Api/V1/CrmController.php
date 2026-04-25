<?php

declare(strict_types=1);

namespace App\Crm\UI\Http\Controllers\Api\V1;

use App\Crm\Application\Command\CreateClient\CreateClientHandler;
use App\Crm\Application\Command\DeleteClient\DeleteClientCommand;
use App\Crm\Application\Command\DeleteClient\DeleteClientHandler;
use App\Crm\Application\Command\UpdateClient\UpdateClientHandler;
use App\Crm\Application\Query\GetClient\GetClientHandler;
use App\Crm\Application\Query\GetClient\GetClientQuery;
use App\Crm\Application\Query\ListClients\ListClientsHandler;
use App\Crm\Domain\Exception\ClientNotFoundException;
use App\Crm\UI\Http\Mappers\CreateClientCommandMapper;
use App\Crm\UI\Http\Mappers\ListClientsQueryMapper;
use App\Crm\UI\Http\Mappers\UpdateClientCommandMapper;
use App\Crm\UI\Http\Requests\V1\CreateClientRequest;
use App\Crm\UI\Http\Requests\V1\UpdateClientRequest;
use App\Shared\Domain\ValueObject\Uuid;
use App\Shared\Infrastructure\Mapper\GenericRequestMapper;
use App\Shared\UI\Http\Controllers\Api\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

final class CrmController extends ApiController
{
    public function __construct(
        private readonly CreateClientHandler $createClientHandler,
        private readonly UpdateClientHandler $updateClientHandler,
        private readonly DeleteClientHandler $deleteClientHandler,
        private readonly GetClientHandler $getClientHandler,
        private readonly ListClientsHandler $listClientsHandler,
        private readonly GenericRequestMapper $mapper,
        private readonly UpdateClientCommandMapper $updateClientCommandMapper,
    ) {
    }

    #[OA\Get(
        path: '/v1/crm',
        summary: 'List all CRM clients',
        tags: ['CRM'],
        security: [['jwt' => []]],
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', description: 'Page number', schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\Parameter(name: 'per_page', in: 'query', description: 'Items per page', schema: new OA\Schema(type: 'integer', default: 20)),
            new OA\Parameter(name: 'status', in: 'query', description: 'Filter by status', schema: new OA\Schema(type: 'string', enum: ['lead', 'prospect', 'active', 'inactive', 'archived'])),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        /** @var ListClientsQueryMapper $queryMapper */
        $queryMapper = $this->mapper->map($request, ListClientsQueryMapper::class);
        $query = $queryMapper->toQuery();

        $result = $this->listClientsHandler->handle($query);

        return $this->success($result->toArray());
    }

    #[OA\Post(
        path: '/v1/crm',
        summary: 'Create a new CRM client',
        tags: ['CRM'],
        security: [['jwt' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'country'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'John Doe'),
                    new OA\Property(property: 'nip', type: 'string', nullable: true, example: '5252674798'),
                    new OA\Property(property: 'country', type: 'string', example: 'Polska'),
                    new OA\Property(property: 'is_company', type: 'boolean', example: true),
                    new OA\Property(property: 'regon', type: 'string', example: '142345678'),
                    new OA\Property(property: 'pesel', type: 'string', example: '82031412345'),
                    new OA\Property(property: 'source', type: 'string', example: 'referral'),
                    new OA\Property(property: 'rating', type: 'integer', example: 5),
                    new OA\Property(property: 'notes', type: 'string'),
                    new OA\Property(property: 'status', type: 'string', enum: ['lead', 'prospect', 'active', 'inactive', 'archived']),
                    new OA\Property(property: 'address_uuid', type: 'string', format: 'uuid'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Client created successfully'),
            new OA\Response(response: 400, description: 'Bad Request'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function store(CreateClientRequest $request): JsonResponse
    {
        /** @var CreateClientCommandMapper $commandMapper */
        $commandMapper = $this->mapper->map($request, CreateClientCommandMapper::class);
        $command = $commandMapper->toCommand();

        $clientDTO = $this->createClientHandler->handle($command);

        return $this->created($clientDTO->toArray());
    }

    #[OA\Get(
        path: '/v1/crm/{id}',
        summary: 'Get CRM client by ID',
        tags: ['CRM'],
        security: [['jwt' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'Client ID',
                schema: new OA\Schema(type: 'string', format: 'uuid')
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Client retrieved successfully'),
            new OA\Response(response: 404, description: 'Client not found'),
        ]
    )]
    public function show(Uuid $id): JsonResponse
    {
        try {
            $query = new GetClientQuery(id: $id);
            $clientDTO = $this->getClientHandler->handle($query);

            return $this->success($clientDTO->toArray());
        } catch (ClientNotFoundException $e) {
            return $this->notFound($e->getMessage());
        }
    }

    #[OA\Put(
        path: '/v1/crm/{id}',
        summary: 'Update an existing CRM client',
        tags: ['CRM'],
        security: [['jwt' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'Client ID',
                schema: new OA\Schema(type: 'string', format: 'uuid')
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'John Doe'),
                    new OA\Property(property: 'nip', type: 'string', example: '5252674798'),
                    new OA\Property(property: 'country', type: 'string', example: 'Polska'),
                    new OA\Property(property: 'is_company', type: 'boolean', example: true),
                    new OA\Property(property: 'regon', type: 'string'),
                    new OA\Property(property: 'pesel', type: 'string'),
                    new OA\Property(property: 'source', type: 'string'),
                    new OA\Property(property: 'rating', type: 'integer'),
                    new OA\Property(property: 'notes', type: 'string'),
                    new OA\Property(property: 'status', type: 'string', enum: ['lead', 'prospect', 'active', 'inactive', 'archived']),
                    new OA\Property(property: 'address_uuid', type: 'string', format: 'uuid'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Client updated successfully'),
            new OA\Response(response: 404, description: 'Client not found'),
        ]
    )]
    public function update(UpdateClientRequest $request, Uuid $id): JsonResponse
    {
        try {
            $command = $this->updateClientCommandMapper->map($request, $id);
            $clientDTO = $this->updateClientHandler->handle($command);

            return $this->success($clientDTO->toArray());
        } catch (ClientNotFoundException $e) {
            return $this->notFound($e->getMessage());
        }
    }

    #[OA\Delete(
        path: '/v1/crm/{id}',
        summary: 'Delete a CRM client',
        tags: ['CRM'],
        security: [['jwt' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'Client ID',
                schema: new OA\Schema(type: 'string', format: 'uuid')
            ),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Client deleted successfully'),
            new OA\Response(response: 404, description: 'Client not found'),
        ]
    )]
    public function destroy(Uuid $id): JsonResponse
    {
        try {
            $command = new DeleteClientCommand(id: $id);
            $this->deleteClientHandler->handle($command);

            return $this->noContent();
        } catch (ClientNotFoundException $e) {
            return $this->notFound($e->getMessage());
        }
    }
}
