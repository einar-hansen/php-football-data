<?php

declare(strict_types=1);

namespace EinarHansen\FootballData\Factories;

use EinarHansen\FootballData\Data\Standing;
use EinarHansen\FootballData\Enums\Stage;
use EinarHansen\Http\Contracts\Data\DataFactory;
use EinarHansen\Http\Support\AttributeBag;

class StandingFactory implements DataFactory
{
    protected readonly TablePositionFactory $tablePositionFactory;

    public function __construct(
        ?TablePositionFactory $tablePositionFactory = null,
    ) {
        $this->tablePositionFactory = $tablePositionFactory ?? new TablePositionFactory();
    }

    public function make(array $attributes): Standing
    {
        $attributes = new AttributeBag($attributes);

        return new Standing(
            stage:      $attributes->enum(key: 'stage', enumClass: Stage::class),
            type:       $attributes->string(key: 'type'),
            group:      $attributes->stringOrNull(key: 'group'),
            positions:  array_map(
                array: $attributes->array(key: 'table'),
                callback: fn ($attributes) => $this->tablePositionFactory->make(
                    attributes: $attributes
                ),
            ),
        );
    }
}
