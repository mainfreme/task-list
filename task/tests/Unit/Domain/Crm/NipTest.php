<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Crm;

use App\Crm\Domain\ValueObject\Nip;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class NipTest extends TestCase
{
    /** Polski NIP z poprawną sumą kontrolną */
    private const VALID_NIP = '5261040828';

    public function test_from_string_throws_on_empty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('NIP cannot be empty');

        Nip::fromString('');
    }

    public function test_from_string_throws_on_wrong_length(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid NIP format. NIP must contain 10 digits');

        Nip::fromString('123456789');
    }

    public function test_from_string_throws_on_invalid_checksum(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid NIP checksum');

        Nip::fromString('5261040829');
    }

    public function test_from_string_accepts_valid_nip(): void
    {
        $nip = Nip::fromString(self::VALID_NIP);

        $this->assertSame(self::VALID_NIP, $nip->getValue());
    }

    public function test_from_string_accepts_nip_with_spaces_and_dashes(): void
    {
        $nip = Nip::fromString('526-104-08-28');

        $this->assertSame('526-104-08-28', $nip->getValue());
    }

    public function test_from_string_throws_on_letters(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid NIP format. NIP must contain 10 digits');

        Nip::fromString('526104082a');
    }
}
