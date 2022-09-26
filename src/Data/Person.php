<?php

declare(strict_types=1);

namespace EinarHansen\FootballData\Data;

use DateTimeInterface;
use EinarHansen\Http\Contracts\Data\Data;

class Person implements Data
{
    /**
     * @param  \EinarHansen\FootballData\Data\Competition[]  $competitions
     */
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly DateTimeInterface $dateOfBirth,
        public readonly string $nationality,
        public readonly DateTimeInterface $updatedAt,
        public readonly ?string $position = null,
        public readonly ?int $shirtNumber = null,
        public readonly ?Team $team = null,
        public readonly ?Contract $contract = null,
        public readonly array $competitions = [],
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'dateOfBirth' => $this->dateOfBirth->format(format: DateTimeInterface::ISO8601),
            'nationality' => $this->nationality,
            'position' => $this->position,
            'shirtNumber' => $this->shirtNumber,
            'team' => $this->team?->toArray(),
            'contract' => $this->contract?->toArray(),
            'competitions' => array_map(
                array: $this->competitions,
                callback: fn (Competition $competition) => $competition->toArray()
            ),
            'updatedAt' => $this->updatedAt->format(format: DateTimeInterface::ISO8601),
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
