<?php

declare(strict_types=1);

namespace EinarHansen\FootballData\Factories;

use EinarHansen\FootballData\Data\Season;
use EinarHansen\FootballData\Data\Team;
use EinarHansen\Http\Contracts\Data\DataFactory;
use EinarHansen\Http\Support\AttributeBag;

class SeasonFactory implements DataFactory
{
    public function __construct(protected readonly TeamFactory $teamFactory = new TeamFactory()) {}

    public function make(array $attributes): Season
    {
        $attributes = new AttributeBag($attributes);

        return new Season(
            id: $attributes->integer(key: 'id'),
            startingAt: $attributes->date(key: 'startDate'),
            endingAt: $attributes->date(key: 'endDate'),
            matchDay: $attributes->integer(key: 'currentMatchday', default: 0),
            team: $attributes->when(
                key: 'winner',
                callback: fn (array $team): Team => $this->teamFactory->make(
                    attributes: $team
                ),
            ),
        );
    }
}
