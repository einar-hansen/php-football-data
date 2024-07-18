<?php

declare(strict_types=1);

namespace EinarHansen\FootballData\Factories;

use EinarHansen\FootballData\Data\FootballMatch;
use EinarHansen\FootballData\Data\Person;
use EinarHansen\FootballData\Enums\Stage;
use EinarHansen\FootballData\Enums\Status;
use EinarHansen\Http\Contracts\Data\DataFactory;
use EinarHansen\Http\Support\AttributeBag;

class FootballMatchFactory implements DataFactory
{
    public function __construct(protected readonly CompetitionFactory $competitionFactory = new CompetitionFactory(), protected readonly PersonFactory $personFactory = new PersonFactory(), protected readonly ScoreFactory $scoreFactory = new ScoreFactory(), protected readonly TeamFactory $teamFactory = new TeamFactory()) {}

    public function make(array $attributes): FootballMatch
    {
        $attributes = new AttributeBag($attributes);

        return new FootballMatch(
            id: $attributes->integer(key: 'id'),
            competition: $this->competitionFactory->make(
                attributes: $attributes->array(key: 'competition') + [
                    'area' => $attributes->arrayOrNull(key: 'area'),
                    'season' => $attributes->arrayOrNull(key: 'season'),
                ],
            ),
            homeTeam: $this->teamFactory->make(
                attributes: $attributes->array(key: 'homeTeam'),
            ),
            awayTeam: $this->teamFactory->make(
                attributes: $attributes->array(key: 'awayTeam'),
            ),
            score: $this->scoreFactory->make(
                attributes: $attributes->array(key: 'score'),
            ),
            matchday: $attributes->integer(key: 'matchday', default: 0),
            startingAt: $attributes->dateTime(key: 'utcDate'),
            status: Status::from($attributes->string(key: 'status')),
            stage: Stage::from($attributes->string(key: 'stage')),
            group: $attributes->stringOrNull(key: 'group'),
            updatedAt: $attributes->dateTime(key: 'lastUpdated'),
            referees: $attributes->map(
                key: 'childAreas',
                callback: fn (array $child): Person => $this->personFactory->make($child)
            )
        );
    }
}
