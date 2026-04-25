<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Ops;

use App\Ops\Domain\ValueObject\DeployRepository;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class DeployRepositoryTest extends TestCase
{
    public function test_from_string_throws_on_empty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Deploy repository cannot be empty');

        DeployRepository::fromString('');
    }

    public function test_from_string_throws_on_whitespace_only(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Deploy repository cannot be empty');

        DeployRepository::fromString("\t\n");
    }

    public function test_from_string_throws_when_exceeds_500_characters(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Deploy repository cannot exceed 500 characters');

        DeployRepository::fromString(str_repeat('b', 501));
    }

    public function test_from_string_accepts_exactly_500_characters(): void
    {
        $value = str_repeat('r', 500);
        $repo = DeployRepository::fromString($value);

        $this->assertSame(500, strlen($repo->getValue()));
    }
}
