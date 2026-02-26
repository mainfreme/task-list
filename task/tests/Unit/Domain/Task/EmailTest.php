<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Task;

use App\Task\Domain\ValueObject\Email;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class EmailTest extends TestCase
{
    public function test_from_string_throws_on_empty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Email cannot be empty');

        Email::fromString('');
    }

    public function test_from_string_throws_on_whitespace_only(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Email cannot be empty');

        Email::fromString('   ');
    }

    public function test_from_string_throws_on_invalid_format_missing_at(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email format');

        Email::fromString('invalidemail.com');
    }

    public function test_from_string_throws_on_invalid_format_missing_domain(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email format');

        Email::fromString('user@');
    }

    public function test_from_string_throws_on_invalid_format_missing_local(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email format');

        Email::fromString('@domain.com');
    }

    public function test_from_string_accepts_valid_email(): void
    {
        $email = Email::fromString('user@example.com');

        $this->assertSame('user@example.com', $email->getValue());
    }

    public function test_from_string_accepts_email_with_subdomain(): void
    {
        $email = Email::fromString('user@mail.example.com');

        $this->assertSame('user@mail.example.com', $email->getValue());
    }

    public function test_from_string_accepts_email_with_plus(): void
    {
        $email = Email::fromString('user+tag@example.com');

        $this->assertSame('user+tag@example.com', $email->getValue());
    }

    public function test_equals_returns_true_for_same_value(): void
    {
        $email1 = Email::fromString('test@example.com');
        $email2 = Email::fromString('test@example.com');

        $this->assertTrue($email1->equals($email2));
    }

    public function test_equals_returns_false_for_different_value(): void
    {
        $email1 = Email::fromString('a@example.com');
        $email2 = Email::fromString('b@example.com');

        $this->assertFalse($email1->equals($email2));
    }
}
