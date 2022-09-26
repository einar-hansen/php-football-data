<?php

declare(strict_types=1);

namespace EinarHansen\FootballData\Data;

use DateTimeInterface;
use EinarHansen\Http\Contracts\Data\Data;

class Competition implements Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $code,
        public readonly string $type,
        public readonly ?string $image = null,
        public readonly ?Area $area = null,
        public readonly ?int $seasonCount = null,
        public readonly ?Season $season = null,
        public readonly ?DateTimeInterface $updatedAt = null,
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
            'code' => $this->code,
            'type' => $this->type,
            'image' => $this->image,
            'area' => $this->area?->toArray(),
            'seasonCount' => $this->seasonCount,
            'season' => $this->season?->toArray(),
            'updatedAt' => $this->updatedAt?->format(format: DateTimeInterface::ISO8601),
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
