<?php

declare(strict_types=1);

namespace EinarHansen\FootballData\Resources;

use DateTimeInterface;
use EinarHansen\FootballData\Data\FootballMatch;
use EinarHansen\FootballData\Enums\Status;
use EinarHansen\FootballData\Exceptions\FootballDataException;
use EinarHansen\FootballData\Factories\FootballMatchFactory;

class MatchResource extends Resource
{
    /**
     * List matches across (a set of) competitions.
     *
     * Optional filters of:
     * ids={matchIds}                   The ids for the matches to filter by
     * competitions={competitionIds}    The ids for the competitions to filter by
     * dateFrom={DATE}                  e.g. 2018-06-22
     * dateTo={DATE}                    e.g. 2018-06-22
     * status={STATUS}                  EinarHansen\FootballData\Enums\Status
     *
     * @param  array<int>|int  $competitionIds
     * @return iterable<int, FootballMatch>|false
     *
     * @throws FootballDataException
     */
    public function all(
        array|int|null $matchIds = null,
        string|DateTimeInterface|null $dateFrom = null,
        string|DateTimeInterface|null $dateTo = null,
        ?Status $status = null,
        array|int|null $competitionIds = null,
    ): iterable|false {
        $response = $this->attempt(
            $this->makeRequest()
                ->withPath(
                    path: '/v4/matches'
                )->withQuery(
                    query: array_filter(
                        array: [
                            'ids' => $matchIds,
                            'competitions' => $competitionIds,
                            'dateFrom' => $dateFrom
                                ? (is_string($dateFrom) ? $dateFrom : $dateFrom->format('Y-m-d'))
                                : null,
                            'dateTo' => $dateTo
                                ? (is_string($dateTo) ? $dateTo : $dateTo->format('Y-m-d'))
                                : null,
                            'status' => $status instanceof Status ? $status->value : null,
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

    /**
     * Show one particular match.
     */
    public function find(int $matchId): FootballMatch|false|null
    {
        $response = $this->attempt(
            $this->makeRequest()
                ->withPath(
                    path: "/v4/matches/{$matchId}"
                )
        );
        if (! $response) {
            return false;
        }

        return $this->makeData(
            response: $response,
            factory: FootballMatchFactory::class
        );
    }

    /**
     * List previous encounters for the teams of a match.
     *
     * Optional filters of:
     * dateFrom={DATE}                  e.g. 2018-06-22
     * dateTo={DATE}                    e.g. 2018-06-22
     * competitions={competitionIds}    The ids for the competitions to filter by
     * limit={LIMIT}                    The number of results
     *
     * @param  array<int>|int  $competitionIds
     * @return iterable<int, FootballMatch>|false
     *
     * @throws FootballDataException
     */
    public function matchHead2Head(
        int $matchId,
        string|DateTimeInterface|null $dateFrom = null,
        string|DateTimeInterface|null $dateTo = null,
        array|int|null $competitionIds = null,
        ?int $limit = null,
    ): iterable|false {
        $response = $this->attempt(
            $this->makeRequest()
                ->withPath(
                    path: "/v4/matches/{$matchId}/head2head"
                )->withQuery(
                    query: array_filter(
                        array: [
                            'competitions' => $competitionIds,
                            'dateFrom' => $dateFrom
                                ? (is_string($dateFrom) ? $dateFrom : $dateFrom->format('Y-m-d'))
                                : null,
                            'dateTo' => $dateTo
                                ? (is_string($dateTo) ? $dateTo : $dateTo->format('Y-m-d'))
                                : null,
                            'limit' => $limit,
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
