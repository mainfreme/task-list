<?php

declare(strict_types=1);

namespace App\Crm\Application\DTO;

use App\Crm\Domain\Dto\ClientDto;

/**
 * @deprecated Use App\Crm\Domain\Dto\ClientDto instead. Kept for backward compatibility.
 */
class_alias(ClientDto::class, 'App\Crm\Application\DTO\ClientDTO');
