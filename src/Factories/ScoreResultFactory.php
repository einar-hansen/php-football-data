<?php

declare(strict_types=1);

namespace EinarHansen\FootballData\Factories;

use EinarHansen\FootballData\Data\ScoreResult;
use EinarHansen\Http\Contracts\Data\DataFactory;
use EinarHansen\Http\Support\AttributeBag;

class ScoreResultFactory implements DataFactory
{
    public function make(array $attributes): ScoreResult
    {
        $attributes = new AttributeBag($attributes);

        return new ScoreResult(
            home: $attributes->integerOrNull(key: 'home'),
            away: $attributes->integerOrNull(key: 'away'),
        );
    }
}
