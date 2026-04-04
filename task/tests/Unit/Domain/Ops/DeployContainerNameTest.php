<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Ops;

use App\Ops\Domain\ValueObject\DeployContainerName;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class DeployContainerNameTest extends TestCase
{
    public function test_from_nullable_returns_null_for_null(): void
    {
        $this->assertNull(DeployContainerName::fromNullable(null));
    }

    public function test_from_nullable_returns_null_for_empty_string(): void
    {
        $this->assertNull(DeployContainerName::fromNullable(''));
    }

    public function test_from_string_throws_when_exceeds_255_characters(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Deploy container name cannot exceed 255 characters');

        DeployContainerName::fromString(str_repeat('c', 256));
    }

    public function test_from_string_accepts_exactly_255_characters(): void
    {
        $value = str_repeat('c', 255);
        $container = DeployContainerName::fromString($value);

        $this->assertSame(255, strlen($container->getValue()));
    }

    public function test_from_string_accepts_single_space(): void
    {
        $container = DeployContainerName::fromString(' ');

        $this->assertSame(' ', $container->getValue());
    }
}
