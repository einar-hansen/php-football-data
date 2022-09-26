<?php

declare(strict_types=1);

namespace EinarHansen\FootballData\Factories;

use EinarHansen\FootballData\Data\Person;
use EinarHansen\Http\Contracts\Data\DataFactory;
use EinarHansen\Http\Support\AttributeBag;

class PersonFactory implements DataFactory
{
    protected readonly CompetitionFactory $competitionFactory;

    protected readonly ContractFactory $contractFactory;

    protected readonly TeamFactory $teamFactory;

    public function __construct(
        ?CompetitionFactory $competitionFactory = null,
        ?ContractFactory $contractFactory = null,
        ?TeamFactory $teamFactory = null,
    ) {
        $this->competitionFactory = $competitionFactory ?? new CompetitionFactory();
        $this->contractFactory = $contractFactory ?? new ContractFactory();
        $this->teamFactory = $teamFactory ?? new TeamFactory();
    }

    public function make(array $attributes): Person
    {
        $attributes = new AttributeBag($attributes);

        $team = $attributes->arrayOrNull(key: 'currentTeam') ?? $attributes->arrayOrNull(key: 'team');
        $contract = $team['contract'] ?? null;
        $area = $team['area'] ?? null;
        $competitions = $team['runningCompetitions'] ?? [];

        return new Person(
            id:             $attributes->integer(key: 'id'),
            name:           $attributes->string(key: 'name'),
            firstName:      $attributes->string(key: 'firstName'),
            lastName:       $attributes->string(key: 'lastName'),
            dateOfBirth:    $attributes->date(key: 'dateOfBirth'),
            nationality:    $attributes->string(key: 'nationality', default: ''),
            position:       $attributes->stringOrNull(key: 'position'),
            shirtNumber:    $attributes->integerOrNull(key: 'shirtNumber'),
            updatedAt:      $attributes->date(key: 'updatedAt'),
            team:           $team ? $this->teamFactory->make(
                attributes: $team
            ) : null,
            contract:       $contract ? $this->contractFactory->make(
                attributes: $contract
            ) : null,
            competitions:   array_map(
                array: $competitions,
                callback: fn ($attributes) => $this->competitionFactory->make(
                    attributes: $attributes + ['area' => $area]
                ),
            ),
        );
    }
}
