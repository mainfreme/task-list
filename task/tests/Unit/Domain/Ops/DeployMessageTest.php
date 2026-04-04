<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Ops;

use App\Ops\Domain\ValueObject\DeployMessage;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class DeployMessageTest extends TestCase
{
    public function test_from_string_accepts_empty_string(): void
    {
        $message = DeployMessage::fromString('');

        $this->assertSame('', $message->getValue());
    }

    public function test_from_string_throws_when_exceeds_10000_characters(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Deploy message cannot exceed 10000 characters');

        DeployMessage::fromString(str_repeat('m', 10001));
    }

    public function test_from_string_accepts_exactly_10000_characters(): void
    {
        $value = str_repeat('m', 10000);
        $message = DeployMessage::fromString($value);

        $this->assertSame(10000, strlen($message->getValue()));
    }
}
