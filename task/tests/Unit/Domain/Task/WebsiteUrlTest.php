<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Task;

use App\Task\Domain\ValueObject\WebsiteUrl;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class WebsiteUrlTest extends TestCase
{
    public function test_from_string_throws_on_empty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Website URL cannot be empty');

        WebsiteUrl::fromString('');
    }

    public function test_from_string_throws_on_invalid_url(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid Website URL format');

        WebsiteUrl::fromString('not-a-url');
    }

    public function test_from_string_throws_on_url_without_scheme(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid Website URL format');

        WebsiteUrl::fromString('www.example.com');
    }

    public function test_from_string_accepts_valid_http_url(): void
    {
        $url = WebsiteUrl::fromString('http://example.com');

        $this->assertSame('http://example.com', $url->getValue());
    }

    public function test_from_string_accepts_valid_https_url(): void
    {
        $url = WebsiteUrl::fromString('https://example.com/path?query=1');

        $this->assertSame('https://example.com/path?query=1', $url->getValue());
    }
}
