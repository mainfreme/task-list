<?php

declare(strict_types=1);

namespace App\Settings\Application\Service;

final class IntegrationCredentialsMasker
{
    private const MASK = '••••••••';

    /**
     * @param array<string, mixed> $credentials
     * @return array<string, mixed>
     */
    public function mask(array $credentials): array
    {
        $out = [];
        foreach ($credentials as $key => $value) {
            if (is_array($value)) {
                $out[$key] = $this->mask($value);
                continue;
            }
            if (is_string($value) && $value !== '' && $this->shouldMaskKey((string) $key)) {
                $out[$key] = self::MASK;
                continue;
            }
            $out[$key] = $value;
        }

        return $out;
    }

    private function shouldMaskKey(string $key): bool
    {
        $normalized = strtolower($key);

        return (bool) preg_match(
            '/(token|secret|password|passwd|apikey|api_key|client_secret|private)/i',
            $normalized
        );
    }
}
