<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Task;

use App\Task\Domain\ValueObject\Title;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class TitleTest extends TestCase
{
    public function test_from_string_throws_on_empty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Title cannot be empty');

        Title::fromString('');
    }

    public function test_from_string_throws_on_whitespace_only(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Title cannot be empty');

        Title::fromString('   ');
    }

    public function test_from_string_throws_when_exceeds_255_characters(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Title cannot exceed 255 characters');

        Title::fromString(str_repeat('a', 256));
    }

    public function test_from_string_accepts_exactly_255_characters(): void
    {
        $title = Title::fromString(str_repeat('a', 255));

        $this->assertSame(255, strlen($title->getValue()));
    }

    public function test_from_string_accepts_single_character(): void
    {
        $title = Title::fromString('A');

        $this->assertSame('A', $title->getValue());
    }
}
