<?php

declare(strict_types=1);

namespace EinarHansen\FootballData\Data;

use DateTimeInterface;
use EinarHansen\FootballData\Enums\Stage;
use EinarHansen\FootballData\Enums\Status;
use EinarHansen\Http\Contracts\Data\Data;

class FootballMatch implements Data
{
    /**
     * @param  Person[]  $referees
     */
    public function __construct(
        public readonly int $id,
        public readonly Competition $competition,
        public readonly Team $homeTeam,
        public readonly Team $awayTeam,
        public readonly Score $score,
        public readonly int $matchday,
        public readonly DateTimeInterface $startingAt,
        public readonly Status $status,
        public readonly Stage $stage,
        public readonly DateTimeInterface $updatedAt,
        public readonly ?string $group = null,
        public readonly array $referees = [],
    ) {}

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'competition' => $this->competition->toArray(),
            'homeTeam' => $this->homeTeam->toArray(),
            'awayTeam' => $this->awayTeam->toArray(),
            'score' => $this->score->toArray(),
            'matchday' => $this->matchday,
            'startingAt' => $this->startingAt->format(format: DateTimeInterface::ISO8601),
            'status' => $this->status->value,
            'stage' => $this->stage->value,
            'updatedAt' => $this->updatedAt->format(format: DateTimeInterface::ISO8601),
            'group' => $this->group,
            'referees' => array_map(
                array: $this->referees,
                callback: fn (Person $person): array => $person->toArray()
            ),
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
