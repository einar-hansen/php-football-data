<?php

declare(strict_types=1);

namespace EinarHansen\FootballData\Tests\Feature\Resources;

use EinarHansen\FootballData\Data\Area;
use EinarHansen\FootballData\FootballDataService;
use EinarHansen\FootballData\Resources\AreaResource;
use EinarHansen\FootballData\Tests\Fixtures\HasResponseFixtures;
use EinarHansen\Http\Contracts\Service\Service;
use Http\Mock\Client;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;

class AreaResourceTest extends TestCase
{
    use HasResponseFixtures;

    public ClientInterface $client;

    public AreaResource $resource;

    public Service $service;

    protected function setUp(): void
    {
        $this->client = new Client();
        $this->service = new FootballDataService(
            client: $this->client
        );
        $this->resource = $this->service->areas();
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
    public function it_can_return_a_collection_of_areas(): void
    {
        $this->client->addResponse(
            response: $this->loadResponseFixture('Area/Areas.json')
        );

        $collection = $this->resource->all();

        $this->assertIsArray(actual: $collection);
        $this->assertCount(272, $collection);
        $this->assertInstanceOf(
            expected: Area::class,
            actual: $collection[array_rand($collection)],
        );
    }

    /**
     * @dataProvider provideAreaData
     * @test
     */
    public function it_can_return_an_area(
        int $areaId,
        string $name,
        string $code,
        string $image,
        int $parentId,
        int $children,
    ): void {
        $this->client->addResponse(
            response: $this->loadResponseFixture("Area/Area-{$areaId}.json")
        );

        $data = $this->resource->find($areaId);

        $this->assertInstanceOf(
            expected: Area::class,
            actual: $data,
        );
        $this->assertIsArray(actual: $data->toArray());
        $this->assertSame($areaId, $data->id);
        $this->assertSame($name, $data->name);
        $this->assertSame($code, $data->code);
        $this->assertSame($image, $data->image);
        $this->assertSame($parentId, $data->parentId);
        $this->assertCount($children, $data->children);
    }

    public function provideAreaData()
    {
        return [
            'England' => [
                2072,
                'England',
                'ENG',
                'https://crests.football-data.org/770.svg',
                2077,
                0,
            ],
            'Germany' => [
                2088,
                'Germany',
                'DEU',
                'https://crests.football-data.org/759.svg',
                2077,
                0,
            ],
            'Italy' => [
                2114,
                'Italy',
                'ITA',
                'https://crests.football-data.org/784.svg',
                2077,
                0,
            ],
            'Europe' => [
                2077,
                'Europe',
                'EUR',
                'https://crests.football-data.org/EUR.svg',
                2267,
                74,
            ],
        ];
    }
}
