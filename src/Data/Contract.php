<?php

declare(strict_types=1);

namespace EinarHansen\FootballData\Data;

use DateTimeInterface;
use EinarHansen\Http\Contracts\Data\Data;

class Contract implements Data
{
    public function __construct(
        public readonly ?DateTimeInterface $start = null,
        public readonly ?DateTimeInterface $end = null,
    ) {}

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return [
            'start' => $this->start?->format(format: DateTimeInterface::ATOM),
            'end' => $this->end?->format(format: DateTimeInterface::ATOM),
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
