<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shared;

use App\Shared\Domain\ValueObject\Uuid;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class UuidTest extends TestCase
{
    private const VALID_UUID_V4 = '550e8400-e29b-41d4-a716-446655440000';

    public function test_from_string_throws_on_invalid_format(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Application ID must be a valid UUID');

        Uuid::fromString('not-a-uuid');
    }

    public function test_from_string_throws_on_empty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Application ID must be a valid UUID');

        Uuid::fromString('');
    }

    public function test_from_string_throws_on_uuid_with_invalid_version(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Application ID must be a valid UUID');

        Uuid::fromString('550e8400-e29b-41d4-a716-44665544000x');
    }

    public function test_from_string_accepts_valid_uuid_v4(): void
    {
        $uuid = Uuid::fromString(self::VALID_UUID_V4);

        $this->assertSame(self::VALID_UUID_V4, $uuid->getValue());
    }

    public function test_equals_returns_true_for_same_value(): void
    {
        $uuid1 = Uuid::fromString(self::VALID_UUID_V4);
        $uuid2 = Uuid::fromString(self::VALID_UUID_V4);

        $this->assertTrue($uuid1->equals($uuid2));
    }

    /** Generate zwraca UUID v7 (Str::uuid7) – weryfikacja regex + round-trip przez fromString */
    public function test_generate_creates_valid_uuid_v7_format(): void
    {
        $uuid = Uuid::generate();
        $value = $uuid->getValue();

        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $value,
            'Generated value must be valid UUID v7'
        );
        $this->assertSame($value, Uuid::fromString($value)->getValue());
    }
}
