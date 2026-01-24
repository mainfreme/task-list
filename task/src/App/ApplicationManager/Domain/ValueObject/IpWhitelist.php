<?php

declare(strict_types=1);

namespace App\ApplicationManager\Domain\ValueObject;

use InvalidArgumentException;

final class IpWhitelist
{
    /**
     * @var string[]
     */
    private readonly array $values;

    /**
     * @param string[] $values
     */
    private function __construct(array $values)
    {
        $this->values = $this->validate($values);
    }

    /**
     * @param string[] $values
     */
    public static function fromArray(array $values): self
    {
        return new self($values);
    }

    /**
     * @param string[]|null $values
     */
    public static function fromNullable(?array $values): ?self
    {
        if ($values === null) {
            return null;
        }

        return new self($values);
    }

    /**
     * @return string[]
     */
    public function toArray(): array
    {
        return $this->values;
    }

    public function isEmpty(): bool
    {
        return count($this->values) === 0;
    }

    public function allows(string $ip): bool
    {
        if (filter_var($ip, FILTER_VALIDATE_IP) === false) {
            return false;
        }

        foreach ($this->values as $entry) {
            if ($entry === $ip) {
                return true;
            }

            if (str_contains($entry, '/') && $this->matchesCidr($ip, $entry)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string[] $values
     * @return string[]
     */
    private function validate(array $values): array
    {
        $normalized = [];

        foreach ($values as $value) {
            if (!is_string($value)) {
                throw new InvalidArgumentException('IP whitelist must contain only strings');
            }

            $value = trim($value);

            if ($value === '') {
                throw new InvalidArgumentException('IP whitelist cannot contain empty values');
            }

            if (!$this->isValidIpOrCidr($value)) {
                throw new InvalidArgumentException('Invalid IP or CIDR value: ' . $value);
            }

            $normalized[] = $value;
        }

        return $normalized;
    }

    private function isValidIpOrCidr(string $value): bool
    {
        if (filter_var($value, FILTER_VALIDATE_IP) !== false) {
            return true;
        }

        if (!str_contains($value, '/')) {
            return false;
        }

        [$ip, $prefix] = explode('/', $value, 2);

        if ($ip === '' || $prefix === '' || !ctype_digit($prefix)) {
            return false;
        }

        $prefixInt = (int) $prefix;

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return $prefixInt >= 0 && $prefixInt <= 32;
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return $prefixInt >= 0 && $prefixInt <= 128;
        }

        return false;
    }

    private function matchesCidr(string $ip, string $cidr): bool
    {
        [$subnet, $prefix] = explode('/', $cidr, 2);
        $prefixInt = (int) $prefix;

        if (filter_var($subnet, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false) {
            return $this->matchesIpv4Cidr($ip, $subnet, $prefixInt);
        }

        if (filter_var($subnet, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false) {
            return $this->matchesIpv6Cidr($ip, $subnet, $prefixInt);
        }

        return false;
    }

    private function matchesIpv4Cidr(string $ip, string $subnet, int $prefix): bool
    {
        $ipLong = ip2long($ip);
        $subnetLong = ip2long($subnet);

        if ($ipLong === false || $subnetLong === false) {
            return false;
        }

        if ($prefix === 0) {
            return true;
        }

        $mask = -1 << (32 - $prefix);

        return (($ipLong & $mask) === ($subnetLong & $mask));
    }

    private function matchesIpv6Cidr(string $ip, string $subnet, int $prefix): bool
    {
        $ipBin = inet_pton($ip);
        $subnetBin = inet_pton($subnet);

        if ($ipBin === false || $subnetBin === false) {
            return false;
        }

        $bytes = intdiv($prefix, 8);
        $bits = $prefix % 8;

        if ($bytes > 0) {
            if (substr($ipBin, 0, $bytes) !== substr($subnetBin, 0, $bytes)) {
                return false;
            }
        }

        if ($bits === 0) {
            return true;
        }

        $mask = (0xFF << (8 - $bits)) & 0xFF;

        return ((ord($ipBin[$bytes]) & $mask) === (ord($subnetBin[$bytes]) & $mask));
    }
}
