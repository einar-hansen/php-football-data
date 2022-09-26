<?php

declare(strict_types=1);

namespace EinarHansen\FootballData\Factories;

use EinarHansen\FootballData\Data\Score;
use EinarHansen\Http\Contracts\Data\DataFactory;
use EinarHansen\Http\Support\AttributeBag;

class ScoreFactory implements DataFactory
{
    protected readonly ScoreResultFactory $scoreResultFactory;

    protected readonly TeamFactory $teamFactory;

    public function __construct(
        ?ScoreResultFactory $scoreResultFactory = null,
        ?TeamFactory $teamFactory = null,
    ) {
        $this->scoreResultFactory = $scoreResultFactory ?? new ScoreResultFactory();
        $this->teamFactory = $teamFactory ?? new TeamFactory();
    }

    public function make(array $attributes): Score
    {
        $attributes = new AttributeBag($attributes);

        return new Score(
            duration:   $attributes->string(key: 'duration'),
            fullTime:   $this->scoreResultFactory->make(
                attributes: $attributes->array(key: 'fullTime'),
            ),
            halfTime:   $this->scoreResultFactory->make(
                attributes: $attributes->array(key: 'halfTime'),
            ),
            winner:     $attributes->string(key: 'winner'),
        );
    }
}
