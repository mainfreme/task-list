<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Settings;

use App\Settings\Application\Command\DeleteIntegrationAccount\DeleteIntegrationAccountCommand;
use App\Settings\Application\Command\DeleteIntegrationAccount\DeleteIntegrationAccountHandler;
use App\Settings\Application\Event\SettingsEventDispatcherInterface;
use App\Settings\Application\Mapper\SettingsEntityMapper;
use App\Settings\Application\Service\IntegrationCredentialsMasker;
use App\Settings\Domain\Entity\IntegrationAccount;
use App\Settings\Domain\Event\SettingsChangedEvent;
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
        $events = Mockery::mock(SettingsEventDispatcherInterface::class);
        $repository->shouldReceive('findById')
            ->once()
            ->andThrow(IntegrationAccountNotFoundException::byId($id->getValue()));
        $repository->shouldNotReceive('softDelete');
        $events->shouldNotReceive('dispatch');

        $handler = new DeleteIntegrationAccountHandler(
            $repository,
            new SettingsEntityMapper(new IntegrationCredentialsMasker()),
            $events
        );

        $this->expectException(IntegrationAccountNotFoundException::class);

        $handler->handle(new DeleteIntegrationAccountCommand(id: $id));
    }

    public function test_handle_dispatches_deleted_event_with_before_credentials_snapshot(): void
    {
        $id = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');
        $account = IntegrationAccount::reconstitute(
            id: $id,
            name: 'X',
            enabled: true,
            externalAccountId: null,
            provider: 'facebook',
            credentials: ['accessToken' => 'secret'],
            createdAt: new DateTimeImmutable(),
            updatedAt: new DateTimeImmutable(),
        );

        $repository = Mockery::mock(IntegrationAccountRepositoryInterface::class);
        $events = Mockery::mock(SettingsEventDispatcherInterface::class);
        $repository->shouldReceive('findById')->once()->andReturn($account);
        $repository->shouldReceive('softDelete')
            ->once()
            ->with(Mockery::on(fn (Uuid $u) => $u->getValue() === $id->getValue()));
        $events->shouldReceive('dispatch')
            ->once()
            ->with(Mockery::on(function (SettingsChangedEvent $event) use ($id) {
                return $event->operation === SettingsChangedEvent::OPERATION_DELETED
                    && $event->resourceId === $id->getValue()
                    && $event->after === null
                    && ($event->before['credentials']['accessToken'] ?? null) === 'secret';
            }));

        $handler = new DeleteIntegrationAccountHandler(
            $repository,
            new SettingsEntityMapper(new IntegrationCredentialsMasker()),
            $events
        );
        $handler->handle(new DeleteIntegrationAccountCommand(id: $id));

        $this->assertTrue(true);
    }
}
