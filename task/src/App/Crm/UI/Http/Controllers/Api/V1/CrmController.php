<?php

declare(strict_types=1);

namespace App\Crm\UI\Http\Controllers\Api\V1;

use App\Crm\Domain\ValueObject\Uuid\ClientId;
use App\Crm\UI\Http\Requests\V1\CreateClientRequest;
use App\Crm\UI\Http\Requests\V1\UpdateClientRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Shared\UI\Http\Controllers\Api\ApiController;

final class CrmController extends ApiController
{
    public function store(CreateClientRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // TODO: Implement handler
        return $this->created($validated, 'Client created successfully');
    }

    public function index(Request $request): JsonResponse
    {
        // TODO: Implement handler
        return $this->success([]);
    }

    public function show(ClientId $id): JsonResponse {
        // TODO: Implement handler
        return $this->success([], 'Client retrieved successfully');
    }

    public function update(UpdateClientRequest $request, ClientId $id): JsonResponse
    {
        $validated = $request->validated();

        // TODO: Implement handler
        return $this->success($validated, 'Client updated successfully');
    }

    public function destroy(ClientId $id): JsonResponse
    {
        // TODO: Implement handler
        return $this->noContent();
    }
}
