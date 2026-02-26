<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shared;

use App\Shared\Domain\ValueObject\Phone;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class PhoneTest extends TestCase
{
    public function test_from_string_throws_on_empty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Phone number cannot be empty');

        Phone::fromString('');
    }

    public function test_from_string_throws_on_whitespace_only(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Phone number cannot be empty');

        Phone::fromString('   ');
    }

    public function test_from_string_throws_on_letters(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid phone number format');

        Phone::fromString('abc123');
    }

    public function test_from_string_throws_on_special_characters(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid phone number format');

        Phone::fromString('+48 123 456 789!');
    }

    public function test_from_string_accepts_digits_only(): void
    {
        $phone = Phone::fromString('123456789');

        $this->assertSame('123456789', $phone->getValue());
    }

    public function test_from_string_accepts_international_format_with_plus(): void
    {
        $phone = Phone::fromString('+48123456789');

        $this->assertSame('+48123456789', $phone->getValue());
    }

    public function test_from_string_accepts_format_with_spaces(): void
    {
        $phone = Phone::fromString('+48 123 456 789');

        $this->assertSame('+48 123 456 789', $phone->getValue());
    }

    public function test_from_string_accepts_format_with_dashes(): void
    {
        $phone = Phone::fromString('+48-123-456-789');

        $this->assertSame('+48-123-456-789', $phone->getValue());
    }

    public function test_from_string_accepts_format_with_spaces_and_dashes(): void
    {
        $phone = Phone::fromString('+48 123-456-789');

        $this->assertSame('+48 123-456-789', $phone->getValue());
    }

    public function test_equals_returns_true_for_same_value(): void
    {
        $phone1 = Phone::fromString('+48123456789');
        $phone2 = Phone::fromString('+48123456789');

        $this->assertTrue($phone1->equals($phone2));
    }

    public function test_equals_returns_false_for_different_value(): void
    {
        $phone1 = Phone::fromString('+48123456789');
        $phone2 = Phone::fromString('+48987654321');

        $this->assertFalse($phone1->equals($phone2));
    }

    /** Przypadek brzegowy: ten sam numer w różnym formacie – equals zwraca false (porównanie stringów) */
    public function test_equals_returns_false_for_same_number_different_format(): void
    {
        $phone1 = Phone::fromString('+48123456789');
        $phone2 = Phone::fromString('+48 123 456 789');

        $this->assertFalse($phone1->equals($phone2));
    }

    public function test_to_string_returns_phone_value(): void
    {
        $phone = Phone::fromString('+48 123 456 789');

        $this->assertSame('+48 123 456 789', (string) $phone);
    }

    public function test_from_string_accepts_short_local_number(): void
    {
        $phone = Phone::fromString('112');

        $this->assertSame('112', $phone->getValue());
    }
}
