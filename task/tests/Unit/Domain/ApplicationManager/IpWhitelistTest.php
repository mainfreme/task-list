<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\ApplicationManager;

use App\ApplicationManager\Domain\ValueObject\IpWhitelist;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class IpWhitelistTest extends TestCase
{
    public function test_from_array_with_empty_array_creates_empty_whitelist(): void
    {
        $whitelist = IpWhitelist::fromArray([]);

        $this->assertTrue($whitelist->isEmpty());
        $this->assertSame([], $whitelist->toArray());
    }

    public function test_allows_returns_false_for_invalid_ip(): void
    {
        $whitelist = IpWhitelist::fromArray(['192.168.1.1']);

        $this->assertFalse($whitelist->allows('not-an-ip'));
        $this->assertFalse($whitelist->allows(''));
        $this->assertFalse($whitelist->allows('256.256.256.256'));
    }

    public function test_allows_with_wildcard_permits_any_valid_ip(): void
    {
        $whitelist = IpWhitelist::fromArray(['*']);

        $this->assertTrue($whitelist->allows('192.168.1.1'));
        $this->assertTrue($whitelist->allows('10.0.0.1'));
        $this->assertTrue($whitelist->allows('::1'));
    }

    public function test_allows_with_exact_ip_match(): void
    {
        $whitelist = IpWhitelist::fromArray(['192.168.1.100', '10.0.0.1']);

        $this->assertTrue($whitelist->allows('192.168.1.100'));
        $this->assertTrue($whitelist->allows('10.0.0.1'));
        $this->assertFalse($whitelist->allows('192.168.1.101'));
    }

    public function test_allows_with_ipv4_cidr_prefix_24(): void
    {
        $whitelist = IpWhitelist::fromArray(['192.168.1.0/24']);

        $this->assertTrue($whitelist->allows('192.168.1.0'));
        $this->assertTrue($whitelist->allows('192.168.1.1'));
        $this->assertTrue($whitelist->allows('192.168.1.255'));
        $this->assertFalse($whitelist->allows('192.168.2.0'));
        $this->assertFalse($whitelist->allows('192.168.0.255'));
    }

    public function test_allows_with_ipv4_cidr_prefix_0_permits_all(): void
    {
        $whitelist = IpWhitelist::fromArray(['0.0.0.0/0']);

        $this->assertTrue($whitelist->allows('0.0.0.0'));
        $this->assertTrue($whitelist->allows('255.255.255.255'));
        $this->assertTrue($whitelist->allows('192.168.1.1'));
    }

    public function test_allows_with_ipv4_cidr_prefix_32_exact_match_only(): void
    {
        $whitelist = IpWhitelist::fromArray(['192.168.1.100/32']);

        $this->assertTrue($whitelist->allows('192.168.1.100'));
        $this->assertFalse($whitelist->allows('192.168.1.101'));
    }

    public function test_from_array_throws_on_empty_string_in_values(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('IP whitelist cannot contain empty values');

        IpWhitelist::fromArray(['192.168.1.1', '']);
    }

    public function test_from_array_throws_on_whitespace_only_value(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('IP whitelist cannot contain empty values');

        IpWhitelist::fromArray(['   ']);
    }

    public function test_from_array_throws_on_invalid_ip(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid IP or CIDR value');

        IpWhitelist::fromArray(['999.999.999.999']);
    }

    public function test_from_array_throws_on_invalid_cidr_prefix(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid IP or CIDR value');

        IpWhitelist::fromArray(['192.168.1.0/33']);
    }

    public function test_from_array_throws_on_non_string_value(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('IP whitelist must contain only strings');

        IpWhitelist::fromArray(['192.168.1.1', 123]);
    }

    public function test_from_nullable_returns_null_for_null(): void
    {
        $this->assertNull(IpWhitelist::fromNullable(null));
    }

    public function test_from_nullable_returns_instance_for_empty_array(): void
    {
        $whitelist = IpWhitelist::fromNullable([]);

        $this->assertInstanceOf(IpWhitelist::class, $whitelist);
        $this->assertTrue($whitelist->isEmpty());
    }

    public function test_from_array_trims_whitespace_from_ips(): void
    {
        $whitelist = IpWhitelist::fromArray(['  192.168.1.1  ']);

        $this->assertTrue($whitelist->allows('192.168.1.1'));
    }

    public function test_is_empty_returns_false_when_has_entries(): void
    {
        $whitelist = IpWhitelist::fromArray(['192.168.1.1']);

        $this->assertFalse($whitelist->isEmpty());
    }
}
