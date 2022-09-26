<?php

declare(strict_types=1);

namespace EinarHansen\FootballData\Factories;

use EinarHansen\FootballData\Data\Season;
use EinarHansen\Http\Contracts\Data\DataFactory;
use EinarHansen\Http\Support\AttributeBag;

class SeasonFactory implements DataFactory
{
    protected readonly TeamFactory $teamFactory;

    public function __construct(
        ?TeamFactory $teamFactory = null,
    ) {
        $this->teamFactory = $teamFactory ?? new TeamFactory();
    }

    public function make(array $attributes): Season
    {
        $attributes = new AttributeBag($attributes);

        return new Season(
            id:                 $attributes->integer(key: 'id'),
            startingAt:         $attributes->date(key: 'startDate'),
            endingAt:           $attributes->date(key: 'endDate'),
            matchDay:           $attributes->integer(key: 'currentMatchday', default: 0),
            winner:             $attributes->when(
                key: 'winner',
                callback: fn (array $team) => $this->teamFactory->make(
                    attributes: $team
                ),
            ),
        );
    }
}
