<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\ApplicationManager;

use App\ApplicationManager\Domain\ValueObject\ApiKey;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ApiKeyTest extends TestCase
{
    private const VALID_KEY_32 = 'a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6';

    public function test_from_string_throws_on_empty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('API Key cannot be empty');

        ApiKey::fromString('');
    }

    public function test_from_string_throws_on_whitespace_only(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('API Key must be at least 32 characters long');

        ApiKey::fromString('   ');
    }

    public function test_from_string_throws_when_31_characters(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('API Key must be at least 32 characters long');

        ApiKey::fromString(str_repeat('a', 31));
    }

    public function test_from_string_accepts_exactly_32_characters(): void
    {
        $apiKey = ApiKey::fromString(self::VALID_KEY_32);

        $this->assertSame(self::VALID_KEY_32, $apiKey->value());
    }

    public function test_from_string_accepts_longer_than_32(): void
    {
        $longKey = str_repeat('a', 64);
        $apiKey = ApiKey::fromString($longKey);

        $this->assertSame($longKey, $apiKey->value());
    }

    public function test_equals_returns_true_for_same_value(): void
    {
        $key1 = ApiKey::fromString(self::VALID_KEY_32);
        $key2 = ApiKey::fromString(self::VALID_KEY_32);

        $this->assertTrue($key1->equals($key2));
    }

    public function test_equals_returns_false_for_different_value(): void
    {
        $key1 = ApiKey::fromString(self::VALID_KEY_32);
        $key2 = ApiKey::fromString(str_repeat('b', 32));

        $this->assertFalse($key1->equals($key2));
    }

    public function test_generate_creates_valid_64_char_hex_key(): void
    {
        $apiKey = ApiKey::generate();

        $this->assertSame(64, strlen($apiKey->value()));
        $this->assertMatchesRegularExpression('/^[a-f0-9]+$/', $apiKey->value());
    }

    public function test_generate_creates_unique_keys(): void
    {
        $key1 = ApiKey::generate();
        $key2 = ApiKey::generate();

        $this->assertNotSame($key1->value(), $key2->value());
    }
}
