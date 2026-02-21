<?php

declare(strict_types=1);

namespace App\Crm\Domain\Dto;

use App\Crm\Domain\Enums\NoteType;
use App\Crm\Domain\ValueObject\IsDeleted;
use App\Crm\Domain\ValueObject\NoteContent;
use App\Crm\Domain\ValueObject\Uuid\NoteId;
use App\Crm\Domain\ValueObject\Uuid\UserId;

final class ClientNoteDto
{
    public function __construct(
        public readonly NoteId $noteId,
        public readonly UserId $userId,
        public NoteContent $content,
        public NoteType $type = NoteType::NOTE,
        public IsDeleted $isDeleted = new IsDeleted(false),
    ) {
    }

    public function softDelete(): void
    {
        $this->isDeleted = IsDeleted::fromBool(true);
    }
}
