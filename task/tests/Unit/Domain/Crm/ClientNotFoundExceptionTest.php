<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Crm;

use App\Crm\Domain\Exception\ClientNotFoundException;
use PHPUnit\Framework\TestCase;

final class ClientNotFoundExceptionTest extends TestCase
{
    /** Wiadomość wyjątku musi zawierać ID – umożliwia debugowanie */
    public function test_by_id_creates_exception_with_message_containing_id(): void
    {
        $id = '550e8400-e29b-41d4-a716-446655440000';

        $exception = ClientNotFoundException::byId($id);

        $this->assertStringContainsString($id, $exception->getMessage());
        $this->assertStringContainsString('Client with ID', $exception->getMessage());
        $this->assertStringContainsString('not found', $exception->getMessage());
    }
}
