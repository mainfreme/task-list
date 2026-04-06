<?php

declare(strict_types=1);

namespace App\ApplicationManager\Domain\Event;

final readonly class ApplicationManagerPersistedEvent
{
    public const OPERATION_CREATED = 'created';

    public const OPERATION_UPDATED = 'updated';

    public const OPERATION_DELETED = 'deleted';

    public function __construct(
        public string $applicationId,
        public string $operation
    ) {
    }
}
