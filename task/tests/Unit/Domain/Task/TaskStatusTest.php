<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Task;

use App\Task\Domain\ValueObject\TaskStatus;
use PHPUnit\Framework\TestCase;
use ValueError;

final class TaskStatusTest extends TestCase
{
    public function test_from_string_throws_on_invalid_value(): void
    {
        $this->expectException(ValueError::class);

        TaskStatus::fromString('invalid_status');
    }

    public function test_from_string_accepts_all_valid_statuses(): void
    {
        $this->assertSame(TaskStatus::PENDING, TaskStatus::fromString('pending'));
        $this->assertSame(TaskStatus::IN_PROGRESS, TaskStatus::fromString('in_progress'));
        $this->assertSame(TaskStatus::COMPLETED, TaskStatus::fromString('completed'));
        $this->assertSame(TaskStatus::CANCELLED, TaskStatus::fromString('cancelled'));
        $this->assertSame(TaskStatus::ARCHIVED, TaskStatus::fromString('archived'));
    }

    /** Wszystkie statusy mają zdefiniowane etykiety po polsku – zmiana w enumie nie umknie */
    public function test_label_returns_polish_translation_for_all_statuses(): void
    {
        $this->assertSame('Oczekuje na realizację', TaskStatus::PENDING->label());
        $this->assertSame('W trakcie realizacji', TaskStatus::IN_PROGRESS->label());
        $this->assertSame('Zakończono', TaskStatus::COMPLETED->label());
        $this->assertSame('Anulowano', TaskStatus::CANCELLED->label());
        $this->assertSame('Zarchiwizowano', TaskStatus::ARCHIVED->label());
    }
}
