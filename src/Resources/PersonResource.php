<?php

declare(strict_types=1);

namespace EinarHansen\FootballData\Resources;

use DateTimeInterface;
use EinarHansen\FootballData\Data\FootballMatch;
use EinarHansen\FootballData\Data\Person;
use EinarHansen\FootballData\Enums\Status;
use EinarHansen\FootballData\Exceptions\FootballDataException;
use EinarHansen\FootballData\Factories\PersonFactory;

class PersonResource extends Resource
{
    /**
     * List one particular person.
     */
    public function find(int $personId): Person|false|null
    {
        $response = $this->attempt(
            $this->makeRequest()
                ->withPath(
                    path: "/v4/persons/{$personId}"
                )
        );
        if (! $response) {
            return false;
        }

        return $this->makeData(
            response: $response,
            factory: PersonFactory::class
        );
    }

    /**
     * Show all matches for a particular person.
     *
     * Optional filters of:
     * dateFrom={DATE}                  e.g. 2018-06-22
     * dateTo={DATE}                    e.g. 2018-06-22
     * status={STATUS}                  EinarHansen\FootballData\Enums\Status
     * competitions={competitionIds}    The ids for the competitions to filter by
     * limit={LIMIT}                    The number of results
     * offset={OFFSET}                  The offset/starting point when paginating
     *
     * @param  array<int>|int  $competitionIds
     * @return iterable<int, FootballMatch>|false
     *
     * @throws FootballDataException
     */
    public function matches(
        Person|int $personId,
        string|DateTimeInterface|null $dateFrom = null,
        string|DateTimeInterface|null $dateTo = null,
        ?Status $status = null,
        array|int|null $competitionIds = null,
        ?int $limit = null,
        ?int $offset = null
    ): iterable|false {
        if ($personId instanceof Person) {
            $personId = $personId->id;
        }

        $response = $this->attempt(
            $this->makeRequest()
                ->withPath(
                    path: "/v4/players/{$personId}/matches"
                )->withQuery(
                    query: array_filter(
                        array: [
                            'dateFrom' => $dateFrom
                                ? (is_string($dateFrom) ? $dateFrom : $dateFrom->format('Y-m-d'))
                                : null,
                            'dateTo' => $dateTo
                                ? (is_string($dateTo) ? $dateTo : $dateTo->format('Y-m-d'))
                                : null,
                            'status' => $status instanceof Status ? $status->value : null,
                            'competitionIds' => $competitionIds,
                            'limit' => $limit,
                            'offset' => $offset,
                        ],
                        callback: fn ($query): bool => ! is_null($query)
                    )
                )
        );
        if (! $response) {
            return false;
        }

        return $this->makeDataCollection(
            response: $response,
            factory: FootballMatchFactory::class,
            pointer: '/matches'
        );
    }
}
