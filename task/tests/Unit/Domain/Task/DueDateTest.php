<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Task;

use App\Task\Domain\ValueObject\DueDate;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class DueDateTest extends TestCase
{
    public function test_from_string_throws_on_invalid_format(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid due date format');

        DueDate::fromString('not-a-date');
    }

    public function test_from_string_throws_on_empty_string(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid due date format');

        DueDate::fromString('');
    }

    public function test_from_string_throws_on_partial_date(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid due date format');

        DueDate::fromString('2025-12');
    }

    public function test_from_string_accepts_date_only_format(): void
    {
        $dueDate = DueDate::fromString('2025-12-31');

        $this->assertSame('2025-12-31', $dueDate->format('Y-m-d'));
    }

    public function test_from_string_accepts_datetime_format(): void
    {
        $dueDate = DueDate::fromString('2025-12-31 14:30:00');

        $this->assertSame('2025-12-31 14:30:00', $dueDate->format('Y-m-d H:i:s'));
    }

    public function test_from_datetime_creates_instance(): void
    {
        $dateTime = new DateTimeImmutable('2025-06-15 10:00:00');

        $dueDate = DueDate::fromDateTime($dateTime);

        $this->assertSame('2025-06-15 10:00:00', $dueDate->format('Y-m-d H:i:s'));
    }

    /** Przypadek brzegowy: fromNullable z null zwraca null */
    public function test_from_nullable_returns_null_for_null(): void
    {
        $result = DueDate::fromNullable(null);

        $this->assertNull($result);
    }

    /** Przypadek brzegowy: fromNullable z prawidłowym stringiem zwraca instancję */
    public function test_from_nullable_returns_instance_for_valid_string(): void
    {
        $result = DueDate::fromNullable('2025-12-31');

        $this->assertInstanceOf(DueDate::class, $result);
        $this->assertSame('2025-12-31', $result->format('Y-m-d'));
    }

    public function test_get_value_returns_datetime_immutable(): void
    {
        $dueDate = DueDate::fromString('2025-12-31');

        $value = $dueDate->getValue();

        $this->assertInstanceOf(DateTimeImmutable::class, $value);
        $this->assertSame('2025-12-31', $value->format('Y-m-d'));
    }

    public function test_format_uses_custom_format(): void
    {
        $dueDate = DueDate::fromString('2025-12-31 14:30:00');

        $this->assertSame('31.12.2025', $dueDate->format('d.m.Y'));
        $this->assertSame('14:30', $dueDate->format('H:i'));
    }

    public function test_format_uses_default_format_when_no_argument(): void
    {
        $dueDate = DueDate::fromString('2025-12-31 14:30:00');

        $this->assertSame('2025-12-31 14:30:00', $dueDate->format());
    }

    public function test_equals_returns_true_for_same_timestamp(): void
    {
        $dueDate1 = DueDate::fromString('2025-12-31 14:30:00');
        $dueDate2 = DueDate::fromString('2025-12-31 14:30:00');

        $this->assertTrue($dueDate1->equals($dueDate2));
    }

    public function test_equals_returns_false_for_different_timestamp(): void
    {
        $dueDate1 = DueDate::fromString('2025-12-31 14:30:00');
        $dueDate2 = DueDate::fromString('2025-12-31 14:30:01');

        $this->assertFalse($dueDate1->equals($dueDate2));
    }

    public function test_is_past_returns_true_for_past_date(): void
    {
        $dueDate = DueDate::fromString('2020-01-01');

        $this->assertTrue($dueDate->isPast());
    }

    public function test_is_past_returns_false_for_future_date(): void
    {
        $dueDate = DueDate::fromString('2099-12-31');

        $this->assertFalse($dueDate->isPast());
    }

    public function test_to_string_returns_default_formatted_date(): void
    {
        $dueDate = DueDate::fromString('2025-12-31 14:30:00');

        $this->assertSame('2025-12-31 14:30:00', (string) $dueDate);
    }
}
