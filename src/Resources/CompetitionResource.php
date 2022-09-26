<?php

declare(strict_types=1);

namespace EinarHansen\FootballData\Resources;

use DateTimeInterface;
use EinarHansen\FootballData\Data\Competition;
use EinarHansen\FootballData\Data\FootballMatch;
use EinarHansen\FootballData\Data\PersonGoalScore;
use EinarHansen\FootballData\Data\Standing;
use EinarHansen\FootballData\Data\Team;
use EinarHansen\FootballData\Enums\Stage;
use EinarHansen\FootballData\Enums\Status;
use EinarHansen\FootballData\Exceptions\FootballDataException;
use EinarHansen\FootballData\Factories\CompetitionFactory;
use EinarHansen\FootballData\Factories\FootballMatchFactory;
use EinarHansen\FootballData\Factories\PersonGoalScoreFactory;
use EinarHansen\FootballData\Factories\StandingFactory;
use EinarHansen\FootballData\Factories\TeamFactory;

class CompetitionResource extends Resource
{
    /**
     * List all available competitions.
     *
     * Optional filter of $areaId
     *
     * @param  int|int[]  $areaIds
     * @return  iterable<int, Competition>|false
     */
    public function all(int|array $areaIds = null): iterable|false
    {
        $response = $this->attempt(
            $this->makeRequest()
                ->withPath(
                    path: '/v4/competitions'
                )->withQuery(
                    query: array_filter(
                        array: ['areas' => $areaIds],
                        callback: fn ($query) => ! is_null($query)
                    )
                )
        );
        if (! $response) {
            return false;
        }

        return $this->makeDataCollection(
            response: $response,
            factory: CompetitionFactory::class,
            pointer: '/competitions'
        );
    }

    /**
     * List one particular competition.
     *
     * @param  int  $competitionId
     */
    public function find(int $competitionId): Competition|false|null
    {
        $response = $this->attempt(
            $this->makeRequest()
            ->withPath(
                path: "/v4/competitions/$competitionId"
            )
        );
        if (! $response) {
            return false;
        }

        return $this->makeData(
            response: $response,
            factory: CompetitionFactory::class
        );
    }

    /**
     * List all matches for a particular competition.
     *
     * Optional filters of:
     * - dateFrom={DATE}          e.g. 2018-06-22
     * - dateTo={DATE}            e.g. 2018-06-22
     * - stage={STAGE}            EinarHansen\FootballData\Enums\Stage
     * - status={STATUS}          EinarHansen\FootballData\Enums\Status
     * - matchday={MATCHDAY}      The round number. Integer /[1-4]+[0-9]
     * - group={GROUP}            Filtering for groupings in a competition
     * - season={YEAR}            The starting year of a season e.g. 2017 or 2016
     *
     * @return  iterable<int, FootballMatch>|false
     *
     * @throws FootballDataException
     */
    public function matches(
        Competition|int $competitionId,
        string|DateTimeInterface $dateFrom = null,
        string|DateTimeInterface $dateTo = null,
        Status $status = null,
        Stage $stage = null,
        int $matchday = null,
        string $group = null,
        int $season = null
    ): iterable|false {
        if ($competitionId instanceof Competition) {
            $competitionId = $competitionId->id;
        }
        $response = $this->attempt(
            $this->makeRequest()
                ->withPath(
                    path: "/v4/competitions/$competitionId/matches"
                )->withQuery(
                    query: array_filter(
                        array: [
                            'dateFrom' => $dateFrom
                                ? (is_string($dateFrom) ? $dateFrom : $dateFrom->format('Y-m-d'))
                                : null,
                            'dateTo' => $dateTo
                                ? (is_string($dateTo) ? $dateTo : $dateTo->format('Y-m-d'))
                                : null,
                            'stage' => $stage ? $stage->value : null,
                            'status' => $status ? $status->value : null,
                            'matchday' => $matchday,
                            'group' => $group,
                            'season' => $season,
                        ],
                        callback: fn ($query) => ! is_null($query)
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
     * Get the standings for a competition.
     *
     * Optional filters of:
     * - matchday={MATCHDAY}    The round number. Integer /[1-4]+[0-9]
     * - season={YEAR}          The starting year of a season e.g. 2017 or 2016
     * - date={DATE}            e.g. 2018-06-22
     *
     * @return  iterable<int, Standing>|false
     *
     * @throws FootballDataException
     */
    public function standings(
        Competition|int $competitionId,
        int $season = null,
        int $matchday = null,
        string|DateTimeInterface $date = null
    ): iterable|false {
        if ($competitionId instanceof Competition) {
            $competitionId = $competitionId->id;
        }
        $response = $this->attempt(
            $this->makeRequest()
                ->withPath(
                    path: "/v4/competitions/$competitionId/standings"
                )->withQuery(
                    query: array_filter(
                        array: [
                            'matchday' => $matchday,
                            'season' => $season,
                            'date' => $date
                                ? (is_string($date) ? $date : $date->format('Y-m-d'))
                                : null,
                        ],
                        callback: fn ($query) => ! is_null($query)
                    )
                )
        );
        if (! $response) {
            return false;
        }

        return $this->makeDataCollection(
            response: $response,
            factory: StandingFactory::class,
            pointer: '/standings'
        );
    }

    /**
     * List all teams for a particular competition.
     *
     * Optional filters of:
     * - season={YEAR}            The starting year of a season e.g. 2017 or 2016
     *
     * @return  iterable<int, Team>|false
     *
     * @throws  FootballDataException
     */
    public function teams(
        Competition|int $competitionId,
        int $season = null,
    ): iterable|false {
        if ($competitionId instanceof Competition) {
            $competitionId = $competitionId->id;
        }
        $response = $this->attempt(
            $this->makeRequest()
                ->withPath(
                    path: "/v4/competitions/$competitionId/teams"
                )->withQuery(
                    query: array_filter(
                        array: [
                            'season' => $season,
                        ],
                        callback: fn ($query) => ! is_null($query)
                    )
                )
        );
        if (! $response) {
            return false;
        }

        return $this->makeDataCollection(
            response: $response,
            factory: TeamFactory::class,
            pointer: '/teams'
        );
    }

    /**
     * List all teams for a particular competition.
     *
     * Optional filters of:
     * - season={YEAR}            The starting year of a season e.g. 2017 or 2016
     * - limit={LIMIT}            The number of results
     *
     * @return  iterable<int, PersonGoalScore>|false
     *
     * @throws FootballDataException
     */
    public function topScorers(
        Competition|int $competitionId,
        int $season = null,
        int $limit = null,
    ): iterable|false {
        if ($competitionId instanceof Competition) {
            $competitionId = $competitionId->id;
        }
        $response = $this->attempt(
            $this->makeRequest()
                ->withPath(
                    path: "/v4/competitions/$competitionId/scorers"
                )->withQuery(
                    query: array_filter(
                        array: [
                            'season' => $season,
                            'limit' => $limit,
                        ],
                        callback: fn ($query) => ! is_null($query)
                    )
                )
        );
        if (! $response) {
            return false;
        }

        return $this->makeDataCollection(
            response: $response,
            factory: PersonGoalScoreFactory::class,
            pointer: '/scorers'
        );
    }
}
