<?php

declare(strict_types=1);

namespace EinarHansen\FootballData\Factories;

use EinarHansen\FootballData\Data\FootballMatch;
use EinarHansen\FootballData\Enums\Stage;
use EinarHansen\FootballData\Enums\Status;
use EinarHansen\Http\Contracts\Data\DataFactory;
use EinarHansen\Http\Support\AttributeBag;

class FootballMatchFactory implements DataFactory
{
    protected readonly CompetitionFactory $competitionFactory;

    protected readonly PersonFactory $personFactory;

    protected readonly ScoreFactory $scoreFactory;

    protected readonly TeamFactory $teamFactory;

    public function __construct(
        ?CompetitionFactory $competitionFactory = null,
        ?PersonFactory $personFactory = null,
        ?ScoreFactory $scoreFactory = null,
        ?TeamFactory $teamFactory = null,
    ) {
        $this->competitionFactory = $competitionFactory ?? new CompetitionFactory();
        $this->personFactory = $personFactory ?? new PersonFactory();
        $this->scoreFactory = $scoreFactory ?? new ScoreFactory();
        $this->teamFactory = $teamFactory ?? new TeamFactory();
    }

    public function make(array $attributes): FootballMatch
    {
        $attributes = new AttributeBag($attributes);

        return new FootballMatch(
            id:             $attributes->integer(key: 'id'),
            competition:    $this->competitionFactory->make(
                attributes: $attributes->array(key: 'competition') + [
                    'area' => $attributes->arrayOrNull(key: 'area'),
                    'season' => $attributes->arrayOrNull(key: 'season'),
                ],
            ),
            homeTeam:       $this->teamFactory->make(
                attributes: $attributes->array(key: 'homeTeam'),
            ),
            awayTeam:       $this->teamFactory->make(
                attributes: $attributes->array(key: 'awayTeam'),
            ),
            score:          $this->scoreFactory->make(
                attributes: $attributes->array(key: 'score'),
            ),
            matchday:       $attributes->integer(key: 'matchday', default: 0),
            startingAt:     $attributes->dateTime(key: 'utcDate'),
            status:         Status::from($attributes->string(key: 'status')),
            stage:          Stage::from($attributes->string(key: 'stage')),
            group:          $attributes->stringOrNull(key: 'group'),
            updatedAt:      $attributes->dateTime(key: 'lastUpdated'),
            referees:       $attributes->map(
                key: 'childAreas',
                callback: fn (array $child) => $this->personFactory->make($child)
            )
        );
    }
}
