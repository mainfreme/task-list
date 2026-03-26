<?php

declare(strict_types=1);

namespace App\Task\Application\DTO;

final readonly class TaskTimeSessionStateDto
{
    public function __construct(
        public bool $isRunning,
        public ?string $currentStartedAt,
        /** Suma sekund z zakończonych sesji (pauza/stop); bieżąca sesja liczona na froncie z current_started_at */
        public int $completedWorkSeconds,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'is_running' => $this->isRunning,
            'current_started_at' => $this->currentStartedAt,
            'completed_work_seconds' => $this->completedWorkSeconds,
        ];
    }
}
