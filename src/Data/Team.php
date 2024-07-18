<?php

declare(strict_types=1);

namespace EinarHansen\FootballData\Data;

use DateTimeInterface;
use EinarHansen\Http\Contracts\Data\Data;

class Team implements Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $shortName,
        public readonly string $code,
        public readonly ?string $image = null,
        public readonly ?string $address = null,
        public readonly ?string $website = null,
        public readonly ?int $founded = null,
        public readonly ?string $colors = null,
        public readonly ?string $venue = null,
        public readonly ?DateTimeInterface $updatedAt = null,
    ) {}

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'shortName' => $this->shortName,
            'code' => $this->code,
            'image' => $this->image,
            'address' => $this->address,
            'website' => $this->website,
            'founded' => $this->founded,
            'colors' => $this->colors,
            'venue' => $this->venue,
            'updatedAt' => $this->updatedAt?->format(format: DateTimeInterface::ATOM),
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
