<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Crm;

use App\Crm\Domain\Enums\ClientStatus;
use PHPUnit\Framework\TestCase;
use ValueError;

final class ClientStatusTest extends TestCase
{
    public function test_from_string_throws_on_invalid_value(): void
    {
        $this->expectException(ValueError::class);

        ClientStatus::fromString('invalid_status');
    }

    public function test_from_string_accepts_all_valid_statuses(): void
    {
        $this->assertSame(ClientStatus::LEAD, ClientStatus::fromString('lead'));
        $this->assertSame(ClientStatus::PROSPECT, ClientStatus::fromString('prospect'));
        $this->assertSame(ClientStatus::ACTIVE, ClientStatus::fromString('active'));
        $this->assertSame(ClientStatus::INACTIVE, ClientStatus::fromString('inactive'));
        $this->assertSame(ClientStatus::ARCHIVED, ClientStatus::fromString('archived'));
    }

    /** Wszystkie statusy mają zdefiniowane etykiety – zmiana w enumie nie umknie */
    public function test_label_returns_translation_for_all_statuses(): void
    {
        $this->assertSame('Lead', ClientStatus::LEAD->label());
        $this->assertSame('Prospect', ClientStatus::PROSPECT->label());
        $this->assertSame('Active', ClientStatus::ACTIVE->label());
        $this->assertSame('Inactive', ClientStatus::INACTIVE->label());
        $this->assertSame('Archived', ClientStatus::ARCHIVED->label());
    }
}
