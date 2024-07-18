<?php

declare(strict_types=1);

namespace EinarHansen\FootballData\Factories;

use EinarHansen\FootballData\Data\Team;
use EinarHansen\Http\Contracts\Data\DataFactory;
use EinarHansen\Http\Support\AttributeBag;

class TeamFactory implements DataFactory
{
    public function make(array $attributes): Team
    {
        $attributes = new AttributeBag($attributes);

        return new Team(
            id: $attributes->integer(key: 'id'),
            name: $attributes->string(key: 'name'),
            shortName: $attributes->string(key: 'shortName'),
            code: $attributes->string(key: 'tla', default: ''),
            image: $attributes->stringOrNull(key: 'crest'),
            address: $attributes->stringOrNull(key: 'address'),
            website: $attributes->stringOrNull(key: 'website'),
            founded: $attributes->integerOrNull(key: 'founded'),
            colors: $attributes->stringOrNull(key: 'colors') ?? $attributes->stringOrNull(key: 'clubColors'),
            venue: $attributes->stringOrNull(key: 'venue'),
            updatedAt: $attributes->dateTimeOrNull(key: 'startDate'),
        );
    }
}
