<?php

declare(strict_types=1);

namespace App\Crm\Domain\Dto;

use App\Crm\Domain\ValueObject\NoteContent;
use App\Crm\Domain\ValueObject\Uuid\NoteId;
use App\Crm\Domain\ValueObject\NoteType;
use App\Crm\Domain\ValueObject\UserId;

final readonly class ClientNoteDto
{
    public function __construct(
        public readonly NoteId $noteId,
        public readonly UserId $userId,
        public NoteContent $content,
        public NoteType $type = NoteType::NOTE,
        public bool $isDeleted = false,
    ) {}

    public function softDelete(): void
    {
        $this->isDeleted = true;
    }
}
