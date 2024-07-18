<?php

declare(strict_types=1);

namespace EinarHansen\FootballData\Factories;

use EinarHansen\FootballData\Data\Standing;
use EinarHansen\FootballData\Data\TablePosition;
use EinarHansen\FootballData\Enums\Stage;
use EinarHansen\Http\Contracts\Data\DataFactory;
use EinarHansen\Http\Support\AttributeBag;

class StandingFactory implements DataFactory
{
    public function __construct(protected readonly TablePositionFactory $tablePositionFactory = new TablePositionFactory()) {}

    public function make(array $attributes): Standing
    {
        $attributes = new AttributeBag($attributes);

        return new Standing(
            stage: $attributes->enum(key: 'stage', enumClass: Stage::class),
            type: $attributes->string(key: 'type'),
            group: $attributes->stringOrNull(key: 'group'),
            positions: array_map(
                array: $attributes->array(key: 'table'),
                callback: fn ($attributes): TablePosition => $this->tablePositionFactory->make(
                    attributes: $attributes
                ),
            ),
        );
    }
}
