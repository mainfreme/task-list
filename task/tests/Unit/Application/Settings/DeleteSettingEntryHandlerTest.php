<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Settings;

use App\Settings\Application\Command\DeleteSettingEntry\DeleteSettingEntryCommand;
use App\Settings\Application\Command\DeleteSettingEntry\DeleteSettingEntryHandler;
use App\Settings\Domain\Entity\SettingEntry;
use App\Settings\Domain\Exception\SettingEntryNotFoundException;
use App\Settings\Domain\Repository\SettingEntryRepositoryInterface;
use App\Settings\Domain\ValueObject\SettingFieldType;
use App\Shared\Domain\ValueObject\Uuid;
use DateTimeImmutable;
use Mockery;
use PHPUnit\Framework\TestCase;

final class DeleteSettingEntryHandlerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_handle_throws_when_not_found(): void
    {
        $id = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');

        $repository = Mockery::mock(SettingEntryRepositoryInterface::class);
        $repository->shouldReceive('findById')
            ->once()
            ->andThrow(SettingEntryNotFoundException::byId($id->getValue()));
        $repository->shouldNotReceive('delete');

        $handler = new DeleteSettingEntryHandler($repository);

        $this->expectException(SettingEntryNotFoundException::class);

        $handler->handle(new DeleteSettingEntryCommand(id: $id));
    }

    public function test_handle_deletes_when_found(): void
    {
        $this->expectNotToPerformAssertions();

        $id = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');
        $entry = SettingEntry::reconstitute(
            id: $id,
            groupKey: 'g',
            fieldKey: 'f',
            fieldType: SettingFieldType::String,
            value: null,
            createdAt: new DateTimeImmutable(),
            updatedAt: new DateTimeImmutable(),
        );

        $repository = Mockery::mock(SettingEntryRepositoryInterface::class);
        $repository->shouldReceive('findById')->once()->andReturn($entry);
        $repository->shouldReceive('delete')->once()->with(Mockery::on(fn (SettingEntry $e) => $e->getId()->getValue() === $id->getValue()));

        $handler = new DeleteSettingEntryHandler($repository);
        $handler->handle(new DeleteSettingEntryCommand(id: $id));
    }
}
