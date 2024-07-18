<?php

declare(strict_types=1);

namespace EinarHansen\FootballData\Data;

use EinarHansen\Http\Contracts\Data\Data;

class TablePosition implements Data
{
    public function __construct(
        public readonly int $position,
        public readonly Team $team,
        public readonly int $playedGames,
        public readonly string $form,
        public readonly int $won,
        public readonly int $draw,
        public readonly int $lost,
        public readonly int $points,
        public readonly int $goalsFor,
        public readonly int $goalsAgainst,
        public readonly int $goalDifference,
    ) {}

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return [
            'position' => $this->position,
            'team' => $this->team->toArray(),
            'playedGames' => $this->playedGames,
            'form' => $this->form,
            'won' => $this->won,
            'draw' => $this->draw,
            'lost' => $this->lost,
            'points' => $this->points,
            'goalsFor' => $this->goalsFor,
            'goalsAgainst' => $this->goalsAgainst,
            'goalDifference' => $this->goalDifference,
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
