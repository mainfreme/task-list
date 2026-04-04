<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Ops;

use App\Ops\Domain\ValueObject\DeployProjectName;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class DeployProjectNameTest extends TestCase
{
    public function test_from_string_throws_on_empty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Deploy project name cannot be empty');

        DeployProjectName::fromString('');
    }

    public function test_from_string_throws_on_whitespace_only(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Deploy project name cannot be empty');

        DeployProjectName::fromString('   ');
    }

    public function test_from_string_throws_when_exceeds_255_characters(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Deploy project name cannot exceed 255 characters');

        DeployProjectName::fromString(str_repeat('a', 256));
    }

    public function test_from_string_accepts_exactly_255_characters(): void
    {
        $value = str_repeat('a', 255);
        $name = DeployProjectName::fromString($value);

        $this->assertSame(255, strlen($name->getValue()));
    }

    public function test_from_string_accepts_trimmed_non_empty_content(): void
    {
        $name = DeployProjectName::fromString(' x ');

        $this->assertSame(' x ', $name->getValue());
    }
}
