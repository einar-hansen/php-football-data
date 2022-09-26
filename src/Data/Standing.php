<?php

declare(strict_types=1);

namespace EinarHansen\FootballData\Data;

use EinarHansen\FootballData\Enums\Stage;
use EinarHansen\Http\Contracts\Data\Data;

class Standing implements Data
{
    /**
     * @param  \EinarHansen\FootballData\Data\TablePosition[]  $positions
     */
    public function __construct(
        public readonly Stage $stage,
        public readonly string $type,
        public readonly ?string $group = null,
        public readonly array $positions = [],
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return [
            'stage' => $this->stage->value,
            'type' => $this->type,
            'group' => $this->group,
            'positions' => array_map(
                array: $this->positions,
                callback: fn (TablePosition $tablePosition) => $tablePosition->toArray()
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
