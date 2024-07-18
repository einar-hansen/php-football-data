<?php

declare(strict_types=1);

namespace EinarHansen\FootballData\Tests\Feature;

use EinarHansen\FootballData\FootballDataService;
use EinarHansen\FootballData\Resources\AreaResource;
use EinarHansen\FootballData\Resources\CompetitionResource;
use EinarHansen\FootballData\Resources\MatchResource;
use EinarHansen\FootballData\Resources\PersonResource;
use EinarHansen\FootballData\Resources\TeamResource;
use EinarHansen\Http\Contracts\Service\Service;
use EinarHansen\Http\Message\RequestFactory;
use Http\Mock\Client;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class FootballDataServiceTest extends TestCase
{
    public Service $service;

    protected function setUp(): void
    {
        $this->service = new FootballDataService(
            apiToken: '::apiToken::',
            baseUri: 'https://api.football-data.org',
            client: new Client()
        );
    }

    #[Test]
    public function it_can_build_a_new_service(): void
    {
        $this->assertInstanceOf(
            expected: Service::class,
            actual: $this->service,
        );
        $this->assertInstanceOf(
            expected: RequestFactory::class,
            actual: $this->service->getRequestFactory(),
        );
    }

    #[Test]
    public function it_can_create_requests(): void
    {
        $request = $this->service->makeRequest()->create();
        $this->assertInstanceOf(
            expected: RequestInterface::class,
            actual: $request,
        );
    }

    #[Test]
    public function it_can_create_a_request_with_other_base_url(): void
    {
        $footballDataService = new FootballDataService(
            baseUri: 'https://example.org',
            client: new Client()
        );
        $request = $footballDataService->makeRequest()->create();
        $this->assertInstanceOf(
            expected: RequestInterface::class,
            actual: $request,
        );
        $this->assertSame(
            expected: 'https://example.org',
            actual: (string) $request->getUri(),
        );
    }

    #[Test]
    public function it_can_create_a_request_without_api_token(): void
    {
        $footballDataService = new FootballDataService(
            apiToken: null,
            client: new Client()
        );
        $request = $footballDataService->makeRequest()->create();
        $this->assertFalse(condition: $request->hasHeader('X-Auth-Token'));
    }

    #[Test]
    public function it_can_create_a_request_with_api_token(): void
    {
        $footballDataService = new FootballDataService(
            apiToken: '::apiToken::',
            client: new Client()
        );
        $request = $footballDataService->makeRequest()->create();
        $this->assertTrue(condition: $request->hasHeader('X-Auth-Token'));
        $this->assertSame(
            expected: '::apiToken::',
            actual: (string) $request->getHeaderLine('X-Auth-Token'),
        );
    }

    /**
        /**
     */
    #[Test]
    public function it_can_send_a_request_and_get_a_response(): void
    {
        $client = new Client();
        $footballDataService = new FootballDataService(client: $client);

        $response = $this->createMock(ResponseInterface::class);
        $client->addResponse($response);

        $request = $footballDataService->makeRequest()->create();
        $returnedResponse = $client->sendRequest($request);

        $this->assertSame(
            expected: $response,
            actual: $returnedResponse
        );
        $this->assertSame(
            expected: $request,
            actual: $client->getLastRequest()
        );
        $this->assertInstanceOf(
            expected: ResponseInterface::class,
            actual: $response,
        );
    }

    #[Test]
    public function it_can_get_areas_resource(): void
    {
        $this->assertInstanceOf(
            expected: AreaResource::class,
            actual: $this->service->areas()
        );
    }

    #[Test]
    public function it_can_get_competitions_resource(): void
    {
        $this->assertInstanceOf(
            expected: CompetitionResource::class,
            actual: $this->service->competitions()
        );
    }

    #[Test]
    public function it_can_get_matches_resource(): void
    {
        $this->assertInstanceOf(
            expected: MatchResource::class,
            actual: $this->service->matches()
        );
    }

    #[Test]
    public function it_can_get_persons_resource(): void
    {
        $this->assertInstanceOf(
            expected: PersonResource::class,
            actual: $this->service->persons()
        );
    }

    #[Test]
    public function it_can_get_teams_resource(): void
    {
        $this->assertInstanceOf(
            expected: TeamResource::class,
            actual: $this->service->teams()
        );
    }
}
