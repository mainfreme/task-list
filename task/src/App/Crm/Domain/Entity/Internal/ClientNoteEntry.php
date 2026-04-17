<?php

declare(strict_types=1);

namespace App\Crm\Domain\Entity\Internal;

use App\Crm\Domain\Enums\NoteType;
use App\Crm\Domain\ValueObject\IsDeleted;
use App\Crm\Domain\ValueObject\NoteContent;
use App\Shared\Domain\ValueObject\Uuid;

/**
 * Notatka osadzona w agregacie klienta.
 *
 * @internal
 */
final class ClientNoteEntry
{
    private IsDeleted $isDeleted;

    public function __construct(
        public readonly Uuid $noteId,
        public readonly Uuid $userId,
        public NoteContent $content,
        public NoteType $type = NoteType::NOTE,
    ) {
        $this->isDeleted = IsDeleted::fromBool(false);
    }

    public function isDeleted(): IsDeleted
    {
        return $this->isDeleted;
    }

    public function softDelete(): void
    {
        $this->isDeleted = IsDeleted::fromBool(true);
    }
}
