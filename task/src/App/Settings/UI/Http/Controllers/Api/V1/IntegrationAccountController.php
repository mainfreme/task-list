<?php

declare(strict_types=1);

namespace App\Settings\UI\Http\Controllers\Api\V1;

use App\Settings\Application\Command\CreateIntegrationAccount\CreateIntegrationAccountCommand;
use App\Settings\Application\Command\CreateIntegrationAccount\CreateIntegrationAccountHandler;
use App\Settings\Application\Command\DeleteIntegrationAccount\DeleteIntegrationAccountCommand;
use App\Settings\Application\Command\DeleteIntegrationAccount\DeleteIntegrationAccountHandler;
use App\Settings\Application\Command\SetIntegrationAccountEnabled\SetIntegrationAccountEnabledCommand;
use App\Settings\Application\Command\SetIntegrationAccountEnabled\SetIntegrationAccountEnabledHandler;
use App\Settings\Application\Command\UpdateIntegrationAccount\UpdateIntegrationAccountCommand;
use App\Settings\Application\Command\UpdateIntegrationAccount\UpdateIntegrationAccountHandler;
use App\Settings\Application\Query\GetIntegrationAccount\GetIntegrationAccountHandler;
use App\Settings\Application\Query\GetIntegrationAccount\GetIntegrationAccountQuery;
use App\Settings\Application\Query\ListIntegrationAccounts\ListIntegrationAccountsHandler;
use App\Settings\Application\Query\ListIntegrationAccounts\ListIntegrationAccountsQuery;
use App\Settings\Domain\Exception\IntegrationAccountNotFoundException;
use App\Settings\UI\Http\Requests\V1\SetIntegrationAccountEnabledRequest;
use App\Settings\UI\Http\Requests\V1\StoreIntegrationAccountRequest;
use App\Settings\UI\Http\Requests\V1\UpdateIntegrationAccountRequest;
use App\Shared\Domain\ValueObject\Uuid;
use App\Shared\UI\Http\Controllers\Api\ApiController;
use Illuminate\Http\JsonResponse;

final class IntegrationAccountController extends ApiController
{
    public function __construct(
        private readonly CreateIntegrationAccountHandler $createHandler,
        private readonly UpdateIntegrationAccountHandler $updateHandler,
        private readonly DeleteIntegrationAccountHandler $deleteHandler,
        private readonly SetIntegrationAccountEnabledHandler $setEnabledHandler,
        private readonly GetIntegrationAccountHandler $getHandler,
        private readonly ListIntegrationAccountsHandler $listHandler,
    ) {
    }

    public function index(): JsonResponse
    {
        $items = $this->listHandler->handle(new ListIntegrationAccountsQuery());

        return $this->success(array_map(
            static fn ($dto) => $dto->toArray(),
            $items
        ));
    }

    public function store(StoreIntegrationAccountRequest $request): JsonResponse
    {
        /** @var array<string, mixed> $credentials */
        $credentials = $request->validated('credentials');

        $dto = $this->createHandler->handle(new CreateIntegrationAccountCommand(
            name: $request->validated('name'),
            enabled: (bool) $request->validated('enabled', true),
            externalAccountId: $request->validated('external_account_id'),
            provider: $request->validated('provider'),
            credentials: $credentials,
        ));

        return $this->created($dto->toArray());
    }

    public function show(Uuid $id): JsonResponse
    {
        try {
            $dto = $this->getHandler->handle(new GetIntegrationAccountQuery(id: $id));

            return $this->success($dto->toArray());
        } catch (IntegrationAccountNotFoundException $e) {
            return $this->notFound($e->getMessage());
        }
    }

    public function update(UpdateIntegrationAccountRequest $request, Uuid $id): JsonResponse
    {
        try {
            /** @var array<string, mixed> $credentials */
            $credentials = $request->validated('credentials');

            $dto = $this->updateHandler->handle(new UpdateIntegrationAccountCommand(
                id: $id,
                name: $request->validated('name'),
                enabled: (bool) $request->validated('enabled'),
                externalAccountId: $request->validated('external_account_id'),
                provider: $request->validated('provider'),
                credentials: $credentials,
            ));

            return $this->success($dto->toArray());
        } catch (IntegrationAccountNotFoundException $e) {
            return $this->notFound($e->getMessage());
        }
    }

    public function destroy(Uuid $id): JsonResponse
    {
        try {
            $this->deleteHandler->handle(new DeleteIntegrationAccountCommand(id: $id));

            return $this->noContent();
        } catch (IntegrationAccountNotFoundException $e) {
            return $this->notFound($e->getMessage());
        }
    }

    public function patchEnabled(SetIntegrationAccountEnabledRequest $request, Uuid $id): JsonResponse
    {
        try {
            $dto = $this->setEnabledHandler->handle(new SetIntegrationAccountEnabledCommand(
                id: $id,
                enabled: (bool) $request->validated('enabled'),
            ));

            return $this->success($dto->toArray());
        } catch (IntegrationAccountNotFoundException $e) {
            return $this->notFound($e->getMessage());
        }
    }
}
