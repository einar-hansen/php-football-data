<?php

declare(strict_types=1);

namespace EinarHansen\FootballData\Factories;

use EinarHansen\FootballData\Data\Score;
use EinarHansen\Http\Contracts\Data\DataFactory;
use EinarHansen\Http\Support\AttributeBag;

class ScoreFactory implements DataFactory
{
    public function __construct(protected readonly ScoreResultFactory $scoreResultFactory = new ScoreResultFactory(), protected readonly TeamFactory $teamFactory = new TeamFactory()) {}

    public function make(array $attributes): Score
    {
        $attributes = new AttributeBag($attributes);

        return new Score(
            duration: $attributes->string(key: 'duration'),
            fullTime: $this->scoreResultFactory->make(
                attributes: $attributes->array(key: 'fullTime'),
            ),
            halfTime: $this->scoreResultFactory->make(
                attributes: $attributes->array(key: 'halfTime'),
            ),
            winner: $attributes->string(key: 'winner'),
        );
    }
}
