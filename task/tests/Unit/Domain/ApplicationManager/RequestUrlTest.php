<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\ApplicationManager;

use App\ApplicationManager\Domain\ValueObject\RequestUrl;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class RequestUrlTest extends TestCase
{
    public function test_from_string_throws_on_empty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Request URL cannot be empty');

        RequestUrl::fromString('');
    }

    public function test_from_string_throws_on_whitespace_only(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Request URL cannot be empty');

        RequestUrl::fromString('   ');
    }

    public function test_from_string_throws_on_invalid_url(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Request URL must be a valid URL');

        RequestUrl::fromString('not-a-url');
    }

    public function test_from_string_throws_on_url_without_scheme(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Request URL must be a valid URL');

        RequestUrl::fromString('www.example.com');
    }

    public function test_from_string_throws_on_url_exceeding_255_characters(): void
    {
        $longUrl = 'https://example.com/' . str_repeat('a', 240);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Request URL cannot exceed 255 characters');

        RequestUrl::fromString($longUrl);
    }

    public function test_from_string_accepts_valid_http_url(): void
    {
        $url = RequestUrl::fromString('http://example.com');

        $this->assertSame('http://example.com', $url->getValue());
    }

    public function test_from_string_accepts_valid_https_url(): void
    {
        $url = RequestUrl::fromString('https://example.com');

        $this->assertSame('https://example.com', $url->getValue());
    }

    public function test_from_string_accepts_url_with_path_and_query(): void
    {
        $urlString = 'https://example.com/callback?token=abc&type=webhook';
        $url = RequestUrl::fromString($urlString);

        $this->assertSame($urlString, $url->getValue());
    }

    public function test_from_string_accepts_url_with_port(): void
    {
        $urlString = 'https://example.com:8443/api';
        $url = RequestUrl::fromString($urlString);

        $this->assertSame($urlString, $url->getValue());
    }

    /** Przypadek brzegowy: dokładnie 255 znaków – powinno przejść */
    public function test_from_string_accepts_url_exactly_255_characters(): void
    {
        $baseUrl = 'https://example.com/';
        $padding = str_repeat('a', 255 - strlen($baseUrl));
        $url = $baseUrl . $padding;

        $this->assertSame(255, strlen($url));

        $requestUrl = RequestUrl::fromString($url);

        $this->assertSame($url, $requestUrl->getValue());
    }

    /** Przypadek brzegowy: fromNullable z null zwraca null */
    public function test_from_nullable_returns_null_for_null(): void
    {
        $result = RequestUrl::fromNullable(null);

        $this->assertNull($result);
    }

    /** Przypadek brzegowy: fromNullable z prawidłowym stringiem zwraca instancję */
    public function test_from_nullable_returns_instance_for_valid_string(): void
    {
        $result = RequestUrl::fromNullable('https://example.com/callback');

        $this->assertInstanceOf(RequestUrl::class, $result);
        $this->assertSame('https://example.com/callback', $result->getValue());
    }

    public function test_equals_returns_true_for_same_value(): void
    {
        $url1 = RequestUrl::fromString('https://example.com/callback');
        $url2 = RequestUrl::fromString('https://example.com/callback');

        $this->assertTrue($url1->equals($url2));
    }

    public function test_equals_returns_false_for_different_value(): void
    {
        $url1 = RequestUrl::fromString('https://example.com/callback1');
        $url2 = RequestUrl::fromString('https://example.com/callback2');

        $this->assertFalse($url1->equals($url2));
    }
}
