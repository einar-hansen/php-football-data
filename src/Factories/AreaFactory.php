<?php

declare(strict_types=1);

namespace EinarHansen\FootballData\Factories;

use EinarHansen\FootballData\Data\Area;
use EinarHansen\Http\Contracts\Data\DataFactory;
use EinarHansen\Http\Support\AttributeBag;

class AreaFactory implements DataFactory
{
    public function make(array $attributes): Area
    {
        $attributes = new AttributeBag($attributes);

        return new Area(
            id:         $attributes->integer(key: 'id'),
            name:       $attributes->string(key: 'name'),
            code:       $attributes->stringOrNull(key: 'countryCode') ?? $attributes->string(key: 'code'),
            image:      $attributes->stringOrNull(key: 'flag'),
            parentId:   $attributes->integerOrNull(key: 'parentAreaId'),
            parentName: $attributes->stringOrNull(key: 'parentArea'),
            children:   $attributes->map(
                key: 'childAreas',
                callback: fn (array $child): Area => $this->make($child)
            )
        );
    }
}
