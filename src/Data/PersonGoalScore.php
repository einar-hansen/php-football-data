<?php

declare(strict_types=1);

namespace EinarHansen\FootballData\Data;

use EinarHansen\Http\Contracts\Data\Data;

class PersonGoalScore implements Data
{
    public function __construct(
        public readonly Person $person,
        public readonly int $goals = 0,
        public readonly int $assists = 0,
        public readonly int $penalties = 0,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return [
            'person' => $this->person->toArray(),
            'goals' => $this->goals,
            'assists' => $this->assists,
            'penalties' => $this->penalties,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
