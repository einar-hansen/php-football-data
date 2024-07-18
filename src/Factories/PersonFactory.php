<?php

declare(strict_types=1);

namespace EinarHansen\FootballData\Factories;

use EinarHansen\FootballData\Data\Competition;
use EinarHansen\FootballData\Data\Person;
use EinarHansen\Http\Contracts\Data\DataFactory;
use EinarHansen\Http\Support\AttributeBag;

class PersonFactory implements DataFactory
{
    public function __construct(protected readonly CompetitionFactory $competitionFactory = new CompetitionFactory(), protected readonly ContractFactory $contractFactory = new ContractFactory(), protected readonly TeamFactory $teamFactory = new TeamFactory()) {}

    public function make(array $attributes): Person
    {
        $attributes = new AttributeBag($attributes);

        $team = $attributes->arrayOrNull(key: 'currentTeam') ?? $attributes->arrayOrNull(key: 'team');
        $contract = $team['contract'] ?? null;
        $area = $team['area'] ?? null;
        $competitions = $team['runningCompetitions'] ?? [];

        return new Person(
            id: $attributes->integer(key: 'id'),
            name: $attributes->string(key: 'name'),
            firstName: $attributes->string(key: 'firstName'),
            lastName: $attributes->string(key: 'lastName'),
            dateOfBirth: $attributes->date(key: 'dateOfBirth'),
            nationality: $attributes->string(key: 'nationality', default: ''),
            position: $attributes->stringOrNull(key: 'position'),
            shirtNumber: $attributes->integerOrNull(key: 'shirtNumber'),
            updatedAt: $attributes->date(key: 'updatedAt'),
            team: $team ? $this->teamFactory->make(
                attributes: $team
            ) : null,
            contract: $contract ? $this->contractFactory->make(
                attributes: $contract
            ) : null,
            competitions: array_map(
                array: $competitions,
                callback: fn ($attributes): Competition => $this->competitionFactory->make(
                    attributes: $attributes + ['area' => $area]
                ),
            ),
        );
    }
}
