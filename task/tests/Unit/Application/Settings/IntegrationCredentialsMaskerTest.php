<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Settings;

use App\Settings\Application\Service\IntegrationCredentialsMasker;
use PHPUnit\Framework\TestCase;

final class IntegrationCredentialsMaskerTest extends TestCase
{
    public function test_masks_string_values_for_sensitive_keys(): void
    {
        $masker = new IntegrationCredentialsMasker();
        $out = $masker->mask([
            'accessToken' => 'secret-token-value',
            'pageId' => '12345',
        ]);

        $this->assertSame('••••••••', $out['accessToken']);
        $this->assertSame('12345', $out['pageId']);
    }

    public function test_masks_nested_sensitive_keys(): void
    {
        $masker = new IntegrationCredentialsMasker();
        $out = $masker->mask([
            'nested' => [
                'client_secret' => 'x',
                'label' => 'ok',
            ],
        ]);

        $this->assertSame('••••••••', $out['nested']['client_secret']);
        $this->assertSame('ok', $out['nested']['label']);
    }
}
