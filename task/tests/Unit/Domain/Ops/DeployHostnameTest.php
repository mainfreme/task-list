<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Ops;

use App\Ops\Domain\ValueObject\DeployHostname;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class DeployHostnameTest extends TestCase
{
    public function test_from_nullable_returns_null_for_null(): void
    {
        $this->assertNull(DeployHostname::fromNullable(null));
    }

    public function test_from_nullable_returns_null_for_empty_string(): void
    {
        $this->assertNull(DeployHostname::fromNullable(''));
    }

    public function test_from_string_throws_when_exceeds_255_characters(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Deploy hostname cannot exceed 255 characters');

        DeployHostname::fromString(str_repeat('h', 256));
    }

    public function test_from_string_accepts_exactly_255_characters(): void
    {
        $value = str_repeat('h', 255);
        $hostname = DeployHostname::fromString($value);

        $this->assertSame(255, strlen($hostname->getValue()));
    }
}
