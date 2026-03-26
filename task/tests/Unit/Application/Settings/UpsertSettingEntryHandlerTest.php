<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Settings;

use App\Settings\Application\Command\UpsertSettingEntry\UpsertSettingEntryCommand;
use App\Settings\Application\Command\UpsertSettingEntry\UpsertSettingEntryHandler;
use App\Settings\Application\Mapper\SettingsEntityMapper;
use App\Settings\Application\Service\IntegrationCredentialsMasker;
use App\Settings\Domain\Entity\SettingEntry;
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

    public function test_handle_creates_when_no_existing_entry(): void
    {
        $mapper = new SettingsEntityMapper(new IntegrationCredentialsMasker());

        $repository = Mockery::mock(SettingEntryRepositoryInterface::class);
        $repository->shouldReceive('findByGroupAndField')
            ->once()
            ->with('g', 'f')
            ->andReturn(null);
        $repository->shouldReceive('save')->once()->with(Mockery::on(function (SettingEntry $e) {
            return $e->getGroupKey() === 'g'
                && $e->getFieldKey() === 'f'
                && $e->getFieldType() === SettingFieldType::String
                && $e->getValue() === 'v';
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
                    value: 'v',
                    createdAt: new DateTimeImmutable('2025-01-01T00:00:00+00:00'),
                    updatedAt: new DateTimeImmutable('2025-01-01T00:00:00+00:00'),
                );
            });

        $handler = new UpsertSettingEntryHandler($repository, $mapper);
        $dto = $handler->handle(new UpsertSettingEntryCommand(
            groupKey: 'g',
            fieldKey: 'f',
            fieldType: 'string',
            value: 'v',
        ));

        $this->assertSame('g', $dto->groupKey);
        $this->assertSame('f', $dto->fieldKey);
        $this->assertSame('string', $dto->fieldType);
        $this->assertSame('v', $dto->value);
    }

    public function test_handle_updates_when_entry_exists(): void
    {
        $mapper = new SettingsEntityMapper(new IntegrationCredentialsMasker());

        $existingId = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');
        $existing = SettingEntry::reconstitute(
            id: $existingId,
            groupKey: 'g',
            fieldKey: 'f',
            fieldType: SettingFieldType::String,
            value: 'old',
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
                && $e->getValue() === 'new';
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
                    value: 'new',
                    createdAt: new DateTimeImmutable('2025-01-01T00:00:00+00:00'),
                    updatedAt: new DateTimeImmutable('2025-01-02T00:00:00+00:00'),
                );
            });

        $handler = new UpsertSettingEntryHandler($repository, $mapper);
        $dto = $handler->handle(new UpsertSettingEntryCommand(
            groupKey: 'g',
            fieldKey: 'f',
            fieldType: 'string',
            value: 'new',
        ));

        $this->assertSame('new', $dto->value);
    }
}
