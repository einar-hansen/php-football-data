<?php

declare(strict_types=1);

namespace EinarHansen\FootballData\Data;

use DateTimeInterface;
use EinarHansen\Http\Contracts\Data\Data;

class Season implements Data
{
    public function __construct(
        public readonly int $id,
        public readonly DateTimeInterface $startingAt,
        public readonly DateTimeInterface $endingAt,
        public readonly int $matchDay = 0,
        public readonly ?Team $team = null,
    ) {}

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'startingAt' => $this->startingAt->format(format: DateTimeInterface::ISO8601),
            'endingAt' => $this->endingAt->format(format: DateTimeInterface::ISO8601),
            'matchDay' => $this->matchDay,
            'winner' => $this->team?->toArray(),
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
