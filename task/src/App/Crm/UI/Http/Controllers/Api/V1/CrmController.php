<?php

declare(strict_types=1);

namespace App\Crm\UI\Http\Controllers\Api\V1;

use App\Crm\Domain\ValueObject\Uuid\ClientId;
use App\Crm\UI\Http\Requests\V1\CreateClientRequest;
use App\Crm\UI\Http\Requests\V1\UpdateClientRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Shared\UI\Http\Controllers\Api\ApiController;
use OpenApi\Attributes as OA;

final class CrmController extends ApiController
{
    #[OA\Get(
        path: "/v1/crm",
        summary: "List all CRM clients",
        tags: ["CRM"],
        security: [["jwt" => []]],
        responses: [
            new OA\Response(response: 200, description: "Successful operation"),
            new OA\Response(response: 401, description: "Unauthenticated")
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        // TODO: Implement handler
        return $this->success([]);
    }

    #[OA\Post(
        path: "/v1/crm",
        summary: "Create a new CRM client",
        tags: ["CRM"],
        security: [["jwt" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "email"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "John Doe"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "john@example.com")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Client created successfully"),
            new OA\Response(response: 400, description: "Bad Request"),
            new OA\Response(response: 401, description: "Unauthenticated")
        ]
    )]
    public function store(CreateClientRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // TODO: Implement handler
        return $this->created($validated, 'Client created successfully');
    }

    #[OA\Get(
        path: "/v1/crm/{id}",
        summary: "Get CRM client by ID",
        tags: ["CRM"],
        security: [["jwt" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "Client ID",
                schema: new OA\Schema(type: "string", format: "uuid")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Client retrieved successfully"),
            new OA\Response(response: 404, description: "Client not found")
        ]
    )]
    public function show(ClientId $id): JsonResponse {
        // TODO: Implement handler
        return $this->success([], 'Client retrieved successfully');
    }

    #[OA\Put(
        path: "/v1/crm/{id}",
        summary: "Update an existing CRM client",
        tags: ["CRM"],
        security: [["jwt" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "Client ID",
                schema: new OA\Schema(type: "string", format: "uuid")
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "name", type: "string", example: "John Doe"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "john@example.com")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Client updated successfully"),
            new OA\Response(response: 404, description: "Client not found")
        ]
    )]
    public function update(UpdateClientRequest $request, ClientId $id): JsonResponse
    {
        $validated = $request->validated();

        // TODO: Implement handler
        return $this->success($validated, 'Client updated successfully');
    }

    #[OA\Delete(
        path: "/v1/crm/{id}",
        summary: "Delete a CRM client",
        tags: ["CRM"],
        security: [["jwt" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "Client ID",
                schema: new OA\Schema(type: "string", format: "uuid")
            )
        ],
        responses: [
            new OA\Response(response: 204, description: "Client deleted successfully"),
            new OA\Response(response: 404, description: "Client not found")
        ]
    )]
    public function destroy(ClientId $id): JsonResponse
    {
        // TODO: Implement handler
        return $this->noContent();
    }
}
