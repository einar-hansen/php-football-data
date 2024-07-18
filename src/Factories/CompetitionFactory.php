<?php

declare(strict_types=1);

namespace EinarHansen\FootballData\Factories;

use DateTimeInterface;
use EinarHansen\FootballData\Data\Competition;
use EinarHansen\Http\Contracts\Data\DataFactory;
use EinarHansen\Http\Support\AttributeBag;

class CompetitionFactory implements DataFactory
{
    public function __construct(protected readonly AreaFactory $areaFactory = new AreaFactory(), protected readonly SeasonFactory $seasonFactory = new SeasonFactory()) {}

    public function make(array $attributes): Competition
    {
        $attributes = new AttributeBag($attributes);

        $season = $attributes->arrayOrNull(key: 'currentSeason') ?? $attributes->arrayOrNull(key: 'season');

        return new Competition(
            id: $attributes->integer(key: 'id'),
            name: $attributes->string(key: 'name'),
            code: $attributes->string(key: 'code'),
            type: $attributes->string(key: 'type'),
            image: $attributes->stringOrNull(key: 'emblem'),
            area: $this->areaFactory->make(
                attributes: $attributes->array(key: 'area')
            ),
            seasonCount: $attributes->integer(key: 'numberOfAvailableSeasons'),
            season: $season ? $this->seasonFactory->make(
                attributes: $season,
            ) : null,
            updatedAt: $attributes->dateTime(
                key: 'lastUpdated',
                format: DateTimeInterface::ATOM
            ),
        );
    }
}
