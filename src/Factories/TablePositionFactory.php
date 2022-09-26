<?php

declare(strict_types=1);

namespace EinarHansen\FootballData\Factories;

use EinarHansen\FootballData\Data\TablePosition;
use EinarHansen\Http\Contracts\Data\DataFactory;
use EinarHansen\Http\Support\AttributeBag;

class TablePositionFactory implements DataFactory
{
    protected readonly TeamFactory $teamFactory;

    public function __construct(
        ?TeamFactory $teamFactory = null,
    ) {
        $this->teamFactory = $teamFactory ?? new TeamFactory();
    }

    public function make(array $attributes): TablePosition
    {
        $attributes = new AttributeBag($attributes);

        return new TablePosition(
            position:       $attributes->integer('position'),
            team:           $this->teamFactory->make(
                attributes: $attributes->array(key: 'team'),
            ),
            playedGames:    $attributes->integer('playedGames'),
            form:           $attributes->string('form'),
            won:            $attributes->integer('won'),
            draw:           $attributes->integer('draw'),
            lost:           $attributes->integer('lost'),
            points:         $attributes->integer('points'),
            goalsFor:       $attributes->integer('goalsFor'),
            goalsAgainst:   $attributes->integer('goalsAgainst'),
            goalDifference: $attributes->integer('goalDifference'),
        );
    }
}
