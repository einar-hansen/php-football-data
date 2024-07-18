<?php

declare(strict_types=1);

namespace EinarHansen\FootballData\Data;

use EinarHansen\Http\Contracts\Data\Data;

class Score implements Data
{
    public function __construct(
        public readonly string $duration,
        public readonly ScoreResult $fullTime,
        public readonly ScoreResult $halfTime,
        public readonly ?string $winner = null,
    ) {}

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return [
            'duration' => $this->duration,
            'fullTime' => $this->fullTime->toArray(),
            'halfTime' => $this->halfTime->toArray(),
            'winner' => $this->winner,
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
