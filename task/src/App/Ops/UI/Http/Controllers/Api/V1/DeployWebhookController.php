<?php

declare(strict_types=1);

namespace App\Ops\UI\Http\Controllers\Api\V1;

use App\Ops\Application\Command\RecordDeployFailure\RecordDeployFailureHandler;
use App\Ops\UI\Http\Mappers\RecordDeployFailureCommandMapper;
use App\Ops\UI\Http\Requests\DeployErrorRequest;
use App\Shared\UI\Http\Controllers\Api\ApiController;
use Illuminate\Http\JsonResponse;

final class DeployWebhookController extends ApiController
{
    public function __construct(
        private readonly RecordDeployFailureHandler $recordDeployFailureHandler,
    ) {
    }

    public function reportError(DeployErrorRequest $request): JsonResponse
    {
        $command = RecordDeployFailureCommandMapper::fromValidatedArray($request->validated());
        $id = $this->recordDeployFailureHandler->handle($command);

        return $this->success(['id' => $id, 'received' => true], 'Deploy error recorded');
    }
}
