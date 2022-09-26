<?php

declare(strict_types=1);

namespace EinarHansen\FootballData\Factories;

use EinarHansen\FootballData\Data\PersonGoalScore;
use EinarHansen\Http\Contracts\Data\DataFactory;
use EinarHansen\Http\Support\AttributeBag;

class PersonGoalScoreFactory implements DataFactory
{
    protected readonly PersonFactory $personFactory;

    public function __construct(
        ?PersonFactory $personFactory = null,
    ) {
        $this->personFactory = $personFactory ?? new PersonFactory();
    }

    public function make(array $attributes): PersonGoalScore
    {
        $attributes = new AttributeBag($attributes);

        return new PersonGoalScore(
            person:         $this->personFactory->make(
                attributes: $attributes->array(key: 'player') + [
                    'team' => $attributes->array(key: 'team'),
                ],
            ),
            goals:          $attributes->integer(key: 'goals'),
            assists:        $attributes->integer(key: 'assists'),
            penalties:      $attributes->integer(key: 'penalties'),
        );
    }
}
