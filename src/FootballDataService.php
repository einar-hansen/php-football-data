<?php

declare(strict_types=1);

namespace EinarHansen\FootballData;

use EinarHansen\FootballData\Exceptions\FootballDataException;
use EinarHansen\FootballData\Resources\AreaResource;
use EinarHansen\FootballData\Resources\CompetitionResource;
use EinarHansen\FootballData\Resources\MatchResource;
use EinarHansen\FootballData\Resources\PersonResource;
use EinarHansen\FootballData\Resources\TeamResource;
use EinarHansen\Http\Contracts\Collection\CollectionFactory;
use EinarHansen\Http\Contracts\RateLimit\RateLimiterState;
use EinarHansen\Http\Contracts\Service\RateLimited;
use EinarHansen\Http\Contracts\Service\Service as ServiceContract;
use EinarHansen\Http\Enum\RequestMethod;
use EinarHansen\Http\Enum\StatusCode;
use EinarHansen\Http\Message\RequestFactory;
use EinarHansen\Http\RateLimit\MemoryRateLimitState;
use EinarHansen\Http\Service\Service;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

final class FootballDataService extends Service implements RateLimited, ServiceContract
{
    public function __construct(
        public readonly ?string $apiToken = null,
        public readonly string $baseUri = 'https://api.football-data.org',
        ?ClientInterface $client = null,
        ?RequestFactoryInterface $requestFactory = null,
        ?UriFactoryInterface $uriFactory = null,
        ?StreamFactoryInterface $streamFactory = null,
        ?CollectionFactory $collectionFactory = null,
        public RateLimiterState $rateLimiterState = new MemoryRateLimitState(
            maxAttempts: 10,
            decaySeconds: 60,
        )
    ) {
        parent::__construct(
            client: $client,
            requestFactory: $requestFactory,
            uriFactory: $uriFactory,
            streamFactory: $streamFactory,
            collectionFactory: $collectionFactory,
        );
    }

    /**
     * {@inheritDoc}
     */
    public function makeRequest(): RequestFactory
    {
        return $this->getRequestFactory()
            ->withUri(
                uri: $this->baseUri
            )->withHeader(
                name: 'Accept',
                value: 'application/json'
            )->when(
                value: $this->apiToken,
                callback: fn ($requestFactory, $token) => $requestFactory->withHeader(
                    name: 'X-Auth-Token',
                    value: $token
                )
            )->withMethod(method: RequestMethod::GET);
    }

    /**
     * {@inheritDoc}
     */
    public function getRateLimitState(): RateLimiterState
    {
        return $this->rateLimiterState;
    }

    /**
     * Attempts to execute a callback if it's not limited. If the
     * limit is reached it will return false.
     *
     * @throws FootballDataException
     */
    public function attempt(RequestFactory $requestFactory): ResponseInterface|false
    {
        /** @var ResponseInterface|false $response */
        $response = $this->rateLimiterState
            ->attempt(fn (): ResponseInterface => $requestFactory->send());

        if ($response) {
            // Since our response contains the exact details of the rate-limit state
            // we are going to manually override/set the states on our state object.
            if ($remaining = $response->getHeaderLine('X-Requests-Available-Minute')) {
                $this->rateLimiterState->setRemaining((int) $remaining);
            }

            if ($expiresIn = $response->getHeaderLine('X-RequestCounter-Reset')) {
                // We add an extra second to avoid hiting the limit, since the
                // quantity of time (ms) in the second might be more than ours.
                $this->rateLimiterState->setExpiresIn(((int) $expiresIn) + 1);
            }

            if ((int) $response->getStatusCode() === StatusCode::BAD_REQUEST->value) {
                /** @var array{ message: string } $body */
                $body = json_decode(
                    json: (string) $response->getBody(),
                    associative: true
                );

                throw new FootballDataException(message: $body['message']);
            }
        }

        return $response;
    }

    public function areas(): AreaResource
    {
        return new AreaResource(
            service: $this,
            collectionFactory: $this->collectionFactory
        );
    }

    public function competitions(): CompetitionResource
    {
        return new CompetitionResource(
            service: $this,
            collectionFactory: $this->collectionFactory
        );
    }

    public function matches(): MatchResource
    {
        return new MatchResource(
            service: $this,
            collectionFactory: $this->collectionFactory
        );
    }

    public function persons(): PersonResource
    {
        return new PersonResource(
            service: $this,
            collectionFactory: $this->collectionFactory
        );
    }

    public function teams(): TeamResource
    {
        return new TeamResource(
            service: $this,
            collectionFactory: $this->collectionFactory
        );
    }
}
