<?php

declare(strict_types=1);

namespace App\Event\UI\Http\Mappers;

use App\Event\Application\Query\ListEvents\ListEventsQuery;
use App\Shared\Domain\ValueObject\Uuid;
use App\Shared\Infrastructure\Mapper\Attribute\MapField;
use App\Shared\Infrastructure\Mapper\Attribute\MapFrom;
use DateTimeImmutable;
use Illuminate\Http\Request;

#[MapFrom(Request::class)]
final class ListEventsQueryMapper
{
    #[MapField]
    public int $page = 1;

    #[MapField('per_page')]
    public int $perPage = 20;

    /** CSV lub pojedynczy UUID (np. `user_ids=id1,id2`) */
    #[MapField('user_ids')]
    public ?string $userIdsCsv = null;

    #[MapField('user_id')]
    public ?string $userId = null;

    /** CSV lub pojedynczy UUID aplikacji (np. `application_ids=id1,id2`) */
    #[MapField('application_ids')]
    public ?string $applicationIdsCsv = null;

    #[MapField('application_id')]
    public ?string $applicationId = null;

    /** CSV lub pojedyncza nazwa modułu */
    #[MapField('modules')]
    public ?string $modulesCsv = null;

    #[MapField('module')]
    public ?string $module = null;

    #[MapField('date_from')]
    public ?string $dateFrom = null;

    #[MapField('date_to')]
    public ?string $dateTo = null;

    #[MapField('sort_dir')]
    public string $sortDir = 'desc';

    public function toQuery(): ListEventsQuery
    {
        return new ListEventsQuery(
            page: max(1, $this->page),
            perPage: max(1, min(200, $this->perPage)),
            userIds: $this->parseUserIds(),
            applicationIds: $this->parseCsvStrings($this->applicationIdsCsv, $this->applicationId),
            modules: $this->parseCsvStrings($this->modulesCsv, $this->module),
            dateFrom: $this->parseDate($this->dateFrom, false),
            dateTo: $this->parseDate($this->dateTo, true),
            sortDir: strtolower($this->sortDir) === 'asc' ? 'asc' : 'desc',
        );
    }

    /**
     * @return list<Uuid>
     */
    private function parseUserIds(): array
    {
        $raw = $this->collectCsv($this->userIdsCsv, $this->userId);
        $out = [];
        foreach ($raw as $value) {
            $out[] = Uuid::fromString($value);
        }

        return $out;
    }

    /**
     * @return list<string>
     */
    private function parseCsvStrings(?string $csv, ?string $single): array
    {
        return $this->collectCsv($csv, $single);
    }

    /**
     * @return list<string>
     */
    private function collectCsv(?string $csv, ?string $single): array
    {
        $values = [];
        if ($csv !== null && $csv !== '') {
            foreach (explode(',', $csv) as $part) {
                $v = trim($part);
                if ($v !== '') {
                    $values[] = $v;
                }
            }
        }
        if ($values === [] && $single !== null && $single !== '') {
            $values[] = $single;
        }

        return array_values(array_unique($values));
    }

    private function parseDate(?string $input, bool $endOfDay): ?DateTimeImmutable
    {
        if ($input === null || $input === '') {
            return null;
        }
        try {
            $date = new DateTimeImmutable($input);
        } catch (\Throwable) {
            return null;
        }

        if ($endOfDay && strlen($input) === 10) {
            $date = $date->setTime(23, 59, 59);
        }

        return $date;
    }
}
