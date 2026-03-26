<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Settings;

use App\Settings\Application\Command\DeleteIntegrationAccount\DeleteIntegrationAccountCommand;
use App\Settings\Application\Command\DeleteIntegrationAccount\DeleteIntegrationAccountHandler;
use App\Settings\Domain\Entity\IntegrationAccount;
use App\Settings\Domain\Exception\IntegrationAccountNotFoundException;
use App\Settings\Domain\Repository\IntegrationAccountRepositoryInterface;
use App\Shared\Domain\ValueObject\Uuid;
use DateTimeImmutable;
use Mockery;
use PHPUnit\Framework\TestCase;

final class DeleteIntegrationAccountHandlerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_handle_does_not_soft_delete_when_not_found(): void
    {
        $id = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');

        $repository = Mockery::mock(IntegrationAccountRepositoryInterface::class);
        $repository->shouldReceive('findById')
            ->once()
            ->andThrow(IntegrationAccountNotFoundException::byId($id->getValue()));
        $repository->shouldNotReceive('softDelete');

        $handler = new DeleteIntegrationAccountHandler($repository);

        $this->expectException(IntegrationAccountNotFoundException::class);

        $handler->handle(new DeleteIntegrationAccountCommand(id: $id));
    }

    public function test_handle_soft_deletes_after_find(): void
    {
        $this->expectNotToPerformAssertions();

        $id = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');
        $account = IntegrationAccount::reconstitute(
            id: $id,
            name: 'X',
            enabled: true,
            externalAccountId: null,
            provider: null,
            credentials: [],
            createdAt: new DateTimeImmutable(),
            updatedAt: new DateTimeImmutable(),
        );

        $repository = Mockery::mock(IntegrationAccountRepositoryInterface::class);
        $repository->shouldReceive('findById')->once()->andReturn($account);
        $repository->shouldReceive('softDelete')
            ->once()
            ->with(Mockery::on(fn (Uuid $u) => $u->getValue() === $id->getValue()));

        $handler = new DeleteIntegrationAccountHandler($repository);
        $handler->handle(new DeleteIntegrationAccountCommand(id: $id));
    }
}
