<?php

declare(strict_types=1);

namespace EinarHansen\FootballData\Resources;

use DateTimeInterface;
use EinarHansen\FootballData\Data\FootballMatch;
use EinarHansen\FootballData\Data\Team;
use EinarHansen\FootballData\Enums\Status;
use EinarHansen\FootballData\Enums\Venue;
use EinarHansen\FootballData\Exceptions\FootballDataException;
use EinarHansen\FootballData\Factories\FootballMatchFactory;
use EinarHansen\FootballData\Factories\TeamFactory;
use EinarHansen\FootballData\Pagination\TeamPaginator;
use EinarHansen\Http\Contracts\Pagination\Paginator;

class TeamResource extends Resource
{
    /**
     * List teams.
     *
     * Optional filters of
     * limit={LIMIT}            The number of results
     * offset={OFFSET}          The offset/starting point when paginating
     *
     * @return iterable<int, Team>|false
     */
    public function paginate(
        int $limit = 50,
        int $page = 1,
    ): iterable|false {
        if ($page <= 0) {
            $page = 1;
        }

        $response = $this->attempt(
            $this->makeRequest()
                ->withPath(
                    path: '/v4/teams'
                )->withQuery(
                    query: [
                        'limit' => (string) $limit,
                        'offset' => (string) ($limit * ($page - 1)),
                    ],
                )
        );
        if (! $response) {
            return false;
        }

        return new TeamPaginator(
            items: $this->makeDataCollection(
                response: $response,
                factory: TeamFactory::class,
                pointer: '/teams'
            ),
            perPage: $limit,
            currentPage: $page,
            resource: $this
        );
    }

    /**
     * List teams, be carefull with this as it will send multiple
     * request as you iterate through the results, until the server
     * is done. You can add maxPage to avoid anarchy.
     *
     * @return iterable<int, Team>|false
     */
    public function all(int $maxPage = 10): iterable|false
    {
        return static::loop(
            paginator: $this->paginate(limit: 50, page: 1),
            maxPage: $maxPage
        );
    }

    /**
     * Show one particular team.
     */
    public function find(int $teamId): Team|false|null
    {
        $response = $this->attempt(
            $this->makeRequest()
                ->withPath(
                    path: "/v4/teams/{$teamId}"
                )
        );
        if (! $response) {
            return false;
        }

        return $this->makeData(
            response: $response,
            factory: TeamFactory::class
        );
    }

    /**
     * List all matches for a particular team.
     *
     * Optional filters of:
     * dateFrom={DATE}                  e.g. 2018-06-22
     * dateTo={DATE}                    e.g. 2018-06-22
     * season={YEAR}                    The starting year of a season e.g. 2017 or 2016
     * competitions={competitionIds}    The ids for the competitions to filter by
     * status={STATUS}                  EinarHansen\FootballData\Enums\Status
     * venue={VENUE}                    EinarHansen\FootballData\Enums\Venue
     * limit={LIMIT}                    The number of results
     *
     * @param  array<int>|int  $competitionIds
     * @return iterable<int, FootballMatch>|false
     *
     * @throws FootballDataException
     */
    public function matches(
        Team|int $teamId,
        string|DateTimeInterface|null $dateFrom = null,
        string|DateTimeInterface|null $dateTo = null,
        ?Status $status = null,
        array|int|null $competitionIds = null,
        ?Venue $venue = null,
        ?int $season = null,
        ?int $limit = null,
    ): iterable|false {
        if ($teamId instanceof Team) {
            $teamId = $teamId->id;
        }

        $response = $this->attempt(
            $this->makeRequest()
                ->withPath(
                    path: "/v4/teams/{$teamId}/matches"
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
                            'venue' => $venue instanceof Venue ? $venue->value : null,
                            'season' => $season,
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

    /**
     * @param  Paginator<int, Team>  $paginator
     * @return iterable<int, Team>
     */
    public static function loop(Paginator $paginator, ?int $maxPage = null): iterable
    {
        foreach ($paginator as $item) {
            yield $item;
        }

        if ($paginator->hasMorePages()) {
            if (! is_null($maxPage) && $paginator->currentPage() >= $maxPage) {
                return null;
            }

            return static::loop(
                paginator: $paginator->nextPage(),
                maxPage: $maxPage
            );
        }
    }
}
