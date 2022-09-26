<?php

declare(strict_types=1);

namespace EinarHansen\FootballData\Factories;

use EinarHansen\FootballData\Data\Contract;
use EinarHansen\Http\Contracts\Data\DataFactory;
use EinarHansen\Http\Support\AttributeBag;

class ContractFactory implements DataFactory
{
    public function make(array $attributes): Contract
    {
        $attributes = new AttributeBag($attributes);

        return new Contract(
            start: $attributes->dateOrNull(key: 'start'),
            end: $attributes->dateOrNull(key: 'end'),
        );
    }
}
