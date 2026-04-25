<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Crm;

use App\Crm\Domain\ValueObject\Nip;
use PHPUnit\Framework\TestCase;

final class NipTest extends TestCase
{
    /** Polski NIP z poprawną sumą kontrolną */
    private const VALID_NIP = '5261040828';

    public function test_tryFrom_returns_null_on_empty(): void
    {
        $this->assertNull(Nip::tryFrom(''));
        $this->assertFalse(Nip::isValid(''));
    }

    public function test_tryFrom_returns_null_on_wrong_length(): void
    {
        $this->assertNull(Nip::tryFrom('123456789'));
        $this->assertFalse(Nip::isValid('123456789'));
    }

    public function test_tryFrom_returns_null_on_invalid_checksum(): void
    {
        $this->assertNull(Nip::tryFrom('5261040829'));
        $this->assertFalse(Nip::isValid('5261040829'));
    }

    public function test_tryFrom_accepts_valid_nip(): void
    {
        $nip = Nip::tryFrom(self::VALID_NIP);
        $this->assertNotNull($nip);
        $this->assertSame(self::VALID_NIP, $nip->getValue());
    }

    public function test_tryFrom_accepts_nip_with_spaces_and_dashes(): void
    {
        $nip = Nip::tryFrom('526-104-08-28');
        $this->assertNotNull($nip);
        $this->assertSame('526-104-08-28', $nip->getValue());
    }

    public function test_tryFrom_returns_null_on_letters(): void
    {
        $this->assertNull(Nip::tryFrom('526104082a'));
        $this->assertFalse(Nip::isValid('526104082a'));
    }
}
