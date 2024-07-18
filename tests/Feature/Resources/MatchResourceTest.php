<?php

declare(strict_types=1);

namespace EinarHansen\FootballData\Tests\Feature\Resources;

use EinarHansen\FootballData\Data\FootballMatch;
use EinarHansen\FootballData\FootballDataService;
use EinarHansen\FootballData\Resources\MatchResource;
use EinarHansen\FootballData\Tests\Fixtures\HasResponseFixtures;
use EinarHansen\Http\Contracts\Service\Service;
use Http\Mock\Client;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;

class MatchResourceTest extends TestCase
{
    use HasResponseFixtures;

    public ClientInterface $client;

    public MatchResource $resource;

    public Service $service;

    protected function setUp(): void
    {
        $this->client = new Client();
        $this->service = new FootballDataService(
            client: $this->client
        );
        $this->resource = $this->service->matches();
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
    public function it_can_return_a_collection_of_matches(): void
    {
        $this->client->addResponse(
            response: $this->loadResponseFixture('Match/Matches.json')
        );

        $collection = $this->resource->all();

        $this->assertIsArray(actual: $collection);
        $this->assertInstanceOf(
            expected: FootballMatch::class,
            actual: $collection[array_rand($collection)],
        );
    }
}
