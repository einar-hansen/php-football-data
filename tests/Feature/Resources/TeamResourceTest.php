<?php

declare(strict_types=1);

namespace EinarHansen\FootballData\Tests\Feature\Resources;

use EinarHansen\FootballData\Data\Team;
use EinarHansen\FootballData\FootballDataService;
use EinarHansen\FootballData\Resources\TeamResource;
use EinarHansen\FootballData\Tests\Fixtures\HasResponseFixtures;
use EinarHansen\Http\Collection\LazyCollectionFactory;
use EinarHansen\Http\Contracts\Service\Service;
use Http\Mock\Client;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;

class TeamResourceTest extends TestCase
{
    use HasResponseFixtures;

    public ClientInterface $client;

    public TeamResource $resource;

    public Service $service;

    protected function setUp(): void
    {
        $this->client = new Client();
        $this->service = new FootballDataService(
            client: $this->client,
            collectionFactory: new LazyCollectionFactory()
        );
        $this->resource = $this->service->teams();
    }

    /**
     * @test
     */
    public function it_can_return_the_service_instance(): void
    {
        $this->assertInstanceOf(
            expected: Service::class,
            actual: $this->resource->service(),
        );
    }

    /**
     * @test
     */
    public function it_returns_a_paginated_collection_of_teams(): void
    {
        $this->client->addResponse(
            response: $this->loadResponseFixture('Team/Teams-page-1.json')
        );
        $collection = $this->resource->paginate(limit: 50, page: 1);
        $this->assertIsIterable(actual: $collection);
        $this->assertIsArray(actual: $collection->toArray());
        $this->assertIsString(actual: json_encode($collection));
        $this->assertSame(expected: 50, actual: $collection->count());
        $this->assertInstanceOf(
            expected: Team::class,
            actual: $collection->first(),
        );
    }

    /**
     * @test
     */
    public function it_returns_all_teams_when_calling_all_collection_of_teams(): void
    {
        $this->client->addResponse(
            response: $this->loadResponseFixture('Team/Teams-page-1.json')
        );
        $this->client->addResponse(
            response: $this->loadResponseFixture('Team/Teams-page-2.json')
        );
        $firstPage = $this->resource->paginate(limit: 50, page: 1);
        $secondPage = $firstPage->nextPage();
        $this->assertIsIterable(actual: $secondPage);
        $this->assertIsArray(actual: $secondPage->toArray());
        $this->assertIsString(actual: json_encode($secondPage));
        $this->assertSame(expected: 50, actual: $secondPage->count());
        $this->assertInstanceOf(
            expected: Team::class,
            actual: $secondPage->first(),
        );
    }
}
