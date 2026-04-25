<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Settings;

use App\Settings\Application\Command\SettingsCommandContext;
use App\Settings\Application\Command\UpsertSettingEntry\UpsertSettingEntryCommand;
use App\Settings\Application\Command\UpsertSettingEntry\UpsertSettingEntryHandler;
use App\Settings\Application\Event\SettingsEventDispatcherInterface;
use App\Settings\Application\Mapper\SettingsEntityMapper;
use App\Settings\Application\Service\IntegrationCredentialsMasker;
use App\Settings\Domain\Entity\SettingEntry;
use App\Settings\Domain\Event\SettingsChangedEvent;
use App\Settings\Domain\Repository\SettingEntryRepositoryInterface;
use App\Settings\Domain\ValueObject\SettingFieldType;
use App\Shared\Domain\ValueObject\Uuid;
use DateTimeImmutable;
use Mockery;
use PHPUnit\Framework\TestCase;

final class UpsertSettingEntryHandlerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_handle_creates_entry_with_null_value_and_dispatches_created_event(): void
    {
        $mapper = new SettingsEntityMapper(new IntegrationCredentialsMasker());
        $eventDispatcher = Mockery::mock(SettingsEventDispatcherInterface::class);

        $repository = Mockery::mock(SettingEntryRepositoryInterface::class);
        $repository->shouldReceive('findByGroupAndField')
            ->once()
            ->with('g', 'f')
            ->andReturn(null);
        $repository->shouldReceive('save')->once()->with(Mockery::on(function (SettingEntry $e) {
            return $e->getGroupKey() === 'g'
                && $e->getFieldKey() === 'f'
                && $e->getFieldType() === SettingFieldType::String
                && $e->getValue() === null;
        }));
        $repository->shouldReceive('findById')
            ->once()
            ->with(Mockery::type(Uuid::class))
            ->andReturnUsing(function (Uuid $id) {
                return SettingEntry::reconstitute(
                    id: $id,
                    groupKey: 'g',
                    fieldKey: 'f',
                    fieldType: SettingFieldType::String,
                    value: null,
                    createdAt: new DateTimeImmutable('2025-01-01T00:00:00+00:00'),
                    updatedAt: new DateTimeImmutable('2025-01-01T00:00:00+00:00'),
                );
            });

        $eventDispatcher->shouldReceive('dispatch')
            ->once()
            ->with(Mockery::on(function (SettingsChangedEvent $event) {
                return $event->operation === SettingsChangedEvent::OPERATION_CREATED
                    && $event->resourceType === 'setting_entry'
                    && $event->before === null
                    && ($event->after['value'] ?? 'unexpected') === null
                    && in_array('value', $event->changedFields, true);
            }));

        $handler = new UpsertSettingEntryHandler($repository, $mapper, $eventDispatcher);
        $dto = $handler->handle(new UpsertSettingEntryCommand(
            groupKey: 'g',
            fieldKey: 'f',
            fieldType: 'string',
            value: null,
        ));

        $this->assertNull($dto->value);
    }

    public function test_handle_updates_without_real_change_dispatches_empty_changed_fields(): void
    {
        $mapper = new SettingsEntityMapper(new IntegrationCredentialsMasker());
        $eventDispatcher = Mockery::mock(SettingsEventDispatcherInterface::class);

        $existingId = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');
        $existing = SettingEntry::reconstitute(
            id: $existingId,
            groupKey: 'g',
            fieldKey: 'f',
            fieldType: SettingFieldType::String,
            value: 'same',
            createdAt: new DateTimeImmutable('2025-01-01T00:00:00+00:00'),
            updatedAt: new DateTimeImmutable('2025-01-01T00:00:00+00:00'),
        );

        $repository = Mockery::mock(SettingEntryRepositoryInterface::class);
        $repository->shouldReceive('findByGroupAndField')
            ->once()
            ->with('g', 'f')
            ->andReturn($existing);
        $repository->shouldReceive('save')->once()->with(Mockery::on(function (SettingEntry $e) use ($existingId) {
            return $e->getId()->getValue() === $existingId->getValue()
                && $e->getValue() === 'same';
        }));
        $repository->shouldReceive('findById')
            ->once()
            ->with(Mockery::on(fn (Uuid $id) => $id->getValue() === $existingId->getValue()))
            ->andReturnUsing(function () use ($existingId) {
                return SettingEntry::reconstitute(
                    id: $existingId,
                    groupKey: 'g',
                    fieldKey: 'f',
                    fieldType: SettingFieldType::String,
                    value: 'same',
                    createdAt: new DateTimeImmutable('2025-01-01T00:00:00+00:00'),
                    updatedAt: new DateTimeImmutable('2025-01-01T00:00:00+00:00'),
                );
            });

        $context = new SettingsCommandContext(
            actorId: '550e8400-e29b-41d4-a716-446655440001',
            requestUrl: 'https://example.test/api/v1/settings/entries',
            ipAddress: '127.0.0.1',
            userAgent: 'phpunit',
        );

        $eventDispatcher->shouldReceive('dispatch')
            ->once()
            ->with(Mockery::on(function (SettingsChangedEvent $event) use ($context, $existingId) {
                return $event->operation === SettingsChangedEvent::OPERATION_UPDATED
                    && $event->resourceId === $existingId->getValue()
                    && $event->changedFields === []
                    && $event->actorId === $context->actorId
                    && $event->requestUrl === $context->requestUrl;
            }));

        $handler = new UpsertSettingEntryHandler($repository, $mapper, $eventDispatcher);
        $dto = $handler->handle(new UpsertSettingEntryCommand(
            groupKey: 'g',
            fieldKey: 'f',
            fieldType: 'string',
            value: 'same',
            context: $context,
        ));

        $this->assertSame('same', $dto->value);
    }
}
