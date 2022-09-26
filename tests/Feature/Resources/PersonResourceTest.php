<?php

declare(strict_types=1);

namespace EinarHansen\FootballData\Tests\Feature\Resources;

use EinarHansen\FootballData\Data\Person;
use EinarHansen\FootballData\FootballDataService;
use EinarHansen\FootballData\Resources\PersonResource;
use EinarHansen\FootballData\Tests\Fixtures\HasResponseFixtures;
use EinarHansen\Http\Contracts\Service\Service;
use Http\Mock\Client;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;

class PersonResourceTest extends TestCase
{
    use HasResponseFixtures;

    public ClientInterface $client;

    public PersonResource $resource;

    public Service $service;

    protected function setUp(): void
    {
        $this->client = new Client();
        $this->service = new FootballDataService(
            client: $this->client
        );
        $this->resource = $this->service->persons();
    }

    /**
     * @test
     */
    public function it_can_return_the_service_instance()
    {
        $this->assertInstanceOf(
            expected: Service::class,
            actual: $this->resource->service(),
        );
    }

    /**
     * @test
     */
    public function it_can_return_a_collection_of_areas()
    {
        $this->client->addResponse(
            response: $this->loadResponseFixture('Person/Person-304.json')
        );

        $person = $this->resource->find(304);

        $this->assertInstanceOf(
            expected: Person::class,
            actual: $person,
        );
    }
}
