<?php

declare(strict_types=1);

namespace EinarHansen\FootballData\Data;

use EinarHansen\Http\Contracts\Data\Data;

class ScoreResult implements Data
{
    public function __construct(
        public readonly ?int $home = null,
        public readonly ?int $away = null,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return [
            'home' => $this->home,
            'away' => $this->away,
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
