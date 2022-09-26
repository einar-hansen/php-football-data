# PHP Football Data

[![Latest Version on Packagist](https://img.shields.io/packagist/v/einar-hansen/php-football-data.svg)](https://packagist.org/packages/einar-hansen/php-football-data)
[![License](https://img.shields.io/packagist/l/einar-hansen/php-football-data.svg)](https://packagist.org/packages/einar-hansen/php-football-data)
[![Total Downloads](https://img.shields.io/packagist/dt/einar-hansen/php-football-data.svg)](https://packagist.org/packages/einar-hansen/php-football-data)

This package gets you quickly up and going with [FootballData's API](https://www.football-data.org/) using PHP. This implementation uses the V4 (latest) of the API, as of the time of writing. Read more about the API in the [quickstart guide](https://www.football-data.org/documentation/quickstart). 

- [Installation](#installation)
- [Getting Started](#getting-started)
- [Initialization](#initialization)
  - [Collections](#collections)
  - [RateLimit](#rateLimit)
- [About the resources](#about-the-resources)
  - [Area Resource](#area-resource)
  - [Competition Resource](#competition-resource)
  - [Match Resource](#match-resource)
  - [Team Resource](#team-resource)
  - [Person Resource](#person-resource)
- [Laravel](#laravel)
- [Credits](#credits)
- [Testing](#testing)
- [About](#about)
- [License](#license)

## Installation

You can install the package via composer:

```bash
composer require einar-hansen/php-football-data
```

## Getting Started

You should [register for an account](https://www.football-data.org/client/register) at https://www.football-data.org.

To use the service you will need create a new instance of `EinarHansen\FootballData\FootballDataService::class`. The service requires a client that implements `Psr\Http\Client\ClientInterface::class`, like for example `GuzzleHttp\Client::class` ('not included in the package'). 

## Initialization
To initialize an API service you don't need to pass in any parameters. But you should provide the API Token that you recieved when registering, to avoid limited access and heavy ratelimiting.

```php
 public function __construct(
    ?string $apiToken = null,
    string $baseUri = 'https://api.football-data.org',
    \Psr\Http\Client\ClientInterface $client = null,
    \Psr\Http\Message\RequestFactoryInterface $requestFactory = null,
    \Psr\Http\Message\UriFactoryInterface $uriFactory = null,
    \Psr\Http\Message\StreamFactoryInterface $streamFactory = null,
    \EinarHansen\Http\Contracts\Collection\CollectionFactory $collectionFactory = null,
    \EinarHansen\Http\Contracts\RateLimit\RateLimiterState $rateLimiterState = null
){}
```

Because of PHP PSR support and [Psr17FactoryDiscovery|Psr18ClientDiscovery](https://docs.php-http.org/en/latest/discovery.html) we do not need to pass in the implementations of the `PSR` contracts.

### Collections
If you want to use the returned data collections as generators, then you can pass an instance of the `EinarHansen\Http\Collection\LazyCollectionFactory::class` class, or yor own implementation of the `EinarHansen\Http\Contracts\Collection\CollectionFactory::class` if you would like. 

```php
$service = new FootballDataService(
    collectionFactory: new \EinarHansen\Http\Collection\LazyCollectionFactory()
);
```

### RateLimit
If you want to keep track of you rate limiting, then you should also provide an instance of `\EinarHansen\Http\Contracts\RateLimit\RateLimiterState::class`. By default the service will remember the attempts and remaining requests in memory, for the duration of the objects lifecycle. Below is an example using the `Psr16RateLimitState` that comes with the package, configured for the `Free Tier`.

Take a look at [football-data pricing page](https://www.football-data.org/pricing) to find the ratelimits for your Tier.

The ratelimiter will update the state with ratelimiting details from the response headers. If you have used up all the attempts, then your requests will return `false`.

```php
$psr6Cache = new \Symfony\Component\Cache\Adapter\FilesystemAdapter();
$psr16Cache = new \Symfony\Component\Cache\Psr16Cache($psr6Cache);

// Or in Laravel, using redis
$psr16Cache = \Illuminate\Support\Facades\Cache::store('redis');

$service = new FootballDataService(
    client: new \GuzzleHttp\Client(),
    rateLimiterState: new \EinarHansen\Http\RateLimit\Psr16RateLimitState(
        cacheKey: 'football-data',  // The key you want to use, any string will do
        cache: $psr16Cache,         // An instance of PSR-16 cache
        maxAttempts: 10,            // 10 calls/minute
        decaySeconds: 60,
    )
);
```

## About the resources

Initialize a service like this.
```php

use EinarHansen\FootballData\FootballDataService;

$service = new FootballDataService(apiToken: '::api-token::');
```

The service consists of 5 resources:

* [Area Resource](#area-resource)
* [Competition Resource](#competition-resource)
* [Match Resource](#match-resource)
* [Team Resource](#team-resource)
* [Person Resource](#person-resource)

Every resource contains at least the methods `all` and `find`. `all` will return a collection of items, while `find` will grab an item by its id.


If the item you are trying to find is not found/doesn't exists, then the resource methods will return `null`. 
If you are beeing rate limited, then the methods will return `false`.

### Area Resource
Read more about the [Area Resource](https://docs.football-data.org/general/v4/area.html) at the offical documentation.

```php
// 👆 Use the $service initialized above
use EinarHansen\FootballData\Data\Area;
use EinarHansen\FootballData\Data\Competition;
use EinarHansen\FootballData\Resources\AreaResource;

$resource = $service->areas(): AreaResource
$collection = $service->areas()->all(): iterable<int, Area>|false;
$area = $service->areas()->find(int $areaId): ?Area|false;

// You can also use the area resource to search for it's competitions
$competitions = $resource->competitions(Area|int $areaId): iterable<int, Competition>|false;
```

### Competition Resource
Read more about the [Competition Resource](https://docs.football-data.org/general/v4/competition.html) at the offical documentation.

```php
// 👆 Use the $service initialized above
use DateTimeInterface;
use EinarHansen\FootballData\Data\Competition;
use EinarHansen\FootballData\Data\FootballMatch;
use EinarHansen\FootballData\Data\Person;
use EinarHansen\FootballData\Data\PersonGoalScore;
use EinarHansen\FootballData\Data\Standing;
use EinarHansen\FootballData\Data\Team;
use EinarHansen\FootballData\Enums\Stage;
use EinarHansen\FootballData\Enums\Status;
use EinarHansen\FootballData\Resources\CompetitionResource;

$resource = $service->competitions(): CompetitionResource;

$collection = $resource->all(int|array $areaIds = null): iterable<int, Competition>|false;
$competition = $resource->find(int $competitionId): ?Competition|false;

// You can also use the competition resource to search for it's matches
$matches = $resource->competitions()->matches(
    Competition|int $competitionId,
    string|DateTimeInterface $dateFrom = null,
    string|DateTimeInterface $dateTo = null,
    Status $status = null,
    Stage $stage = null,
    int $matchday = null,
    string $group = null,
    int $season = null
): iterable<int, FootballMatch>|false;

// Or the current standings
$standings = $resource->competitions()->standings(
    Competition|int $competitionId,
    int $season = null,
    int $matchday = null,
    string|DateTimeInterface $date = null
): iterable<int, Standing>|false;

// Or the teams
$teams = $resource->competitions()->teams(
    Competition|int $competitionId,
    int $season = null,
): iterable<int, Team>|false;

// Or the topScorers
$topScorers = $resource->competitions()->topScorers(
    Competition|int $competitionId,
    int $season = null,
    int $limit = null,
): iterable<int, PersonGoalScore>|false;
```

### Match Resource
Read more about the [Match Resource](https://docs.football-data.org/general/v4/match.html) at the offical documentation.
```php
// 👆 Use the $service initialized above
use DateTimeInterface;
use EinarHansen\FootballData\Data\FootballMatch;
use EinarHansen\FootballData\Enums\Status;
use EinarHansen\FootballData\Resources\MatchResource;

$resource = $service->matches(): MatchResource
$collection = $resource->all(
    array|int $matchIds = null,
    string|DateTimeInterface $dateFrom = null,
    string|DateTimeInterface $dateTo = null,
    Status $status = null,
    array|int $competitionIds = null,
): iterable<int, FootballMatch>|false;
$match = $service->matches()->find(int $matchId): ?FootballMatch|false;

// And you can show the previous matches between the teams
$matches = $service->matches()->matchHead2Head(
    int $matchId,
    string|DateTimeInterface $dateFrom = null,
    string|DateTimeInterface $dateTo = null,
    array|int $competitionIds = null,
    int $limit = null,
): iterable<int, FootballMatch>|false;
```

### Person Resource
Read more about the [Person Resource](https://docs.football-data.org/general/v4/person.html) at the offical documentation.

```php
// 👆 Use the $service initialized above
use DateTimeInterface;
use EinarHansen\FootballData\Data\FootballMatch;
use EinarHansen\FootballData\Data\Person;
use EinarHansen\FootballData\Enums\Status;
use EinarHansen\FootballData\Resources\PersonResource;

$resource = $service->persons(): PersonResource
$person = $resource->find(int $personId): ?Person|false

// You can list matches the person was involved in
$matches = $resource->matches(
    Person|int $personId,
    string|DateTimeInterface $dateFrom = null,
    string|DateTimeInterface $dateTo = null,
    Status $status = null,
    array|int $competitionIds = null,
    int $limit = null,
    int $offset = null
): iterable<int, FootballMatch>|false;
```

### Team Resource
Read more about the [Team Resource](https://docs.football-data.org/general/v4/team.html) at the offical documentation.

```php
// 👆 Use the $service initialized above
use DateTimeInterface;
use EinarHansen\FootballData\Data\FootballMatch;
use EinarHansen\FootballData\Data\Person;
use EinarHansen\FootballData\Data\Team;
use EinarHansen\FootballData\Enums\Status;
use EinarHansen\FootballData\Resources\TeamResource;
use EinarHansen\FootballData\Pagination\TeamPaginator;

$resource = $service->teams(): TeamResource
$team = $resource->find(int $teamId): ?Team|false;

// This is the preferred method, as it returns a TeamPaginator
$collection = $resource->paginate(
    int $limit = 50,
    int $page = 1
): TeamPaginator<int, Team>|false;

// This method will iterate the results on every page until completed. 
// Set a max page count to avoid an uncontrollable loop.
$collection = $resource->all(int $maxPage = 10): iterable<int, Team>|false;

// You can list matches the person was involved in
$matches = $resource->matches(
    Person|int $personId,
    string|DateTimeInterface $dateFrom = null,
    string|DateTimeInterface $dateTo = null,
    Status $status = null,
    array|int $competitionIds = null,
    int $limit = null,
    int $offset = null
): iterable<int, FootballMatch>|false;
```

## Paginator
The Team Resource has a paginate method that returns an instance of the contract `EinarHansen\Http\Contracts\Pagination\Paginator::class` that keeps track of the pages and items. The Team Resource uses `EinarHansen\FootballData\Pagination\TeamPaginator::class`. The paginator allows you to jump between pages with the `nextPage` and `previousPage` methods.

```php
// Go to first page
$page1 = $service->teams()->paginate(limit: 50, page: 1);

// Jump between pages, every time you call this method a request is sent and items are loaded.
$page2 = $page1->nextPage();        // Paginator for page 2
$page3 = $page2->nextPage();        // Paginator for page 3
$page2 = $page3->previousPage();    // Paginator for page 2

$page2->items();        // array<int, Team>
$page2->count();        // (int) 50
$page2->isEmpty();      // (bool) false
$page2->isNotEmpty();   // (bool) true
json_encode($page2);    // string containing all items as an array
```

## Laravel

If you are using Laravel, then you might want to add your `API Token` to your `.env` file and reference it from you config file. You might also want to register it as a singleton in one of your service providers, for example `App\Providers\AppServiceProvider::class`.
    
```php
use EinarHansen\FootballData\FootballDataService;
use EinarHansen\Http\RateLimit\Psr16RateLimitState;
use GuzzleHttp\Client;

...
class AppServiceProvider extends ServiceProvider

public function register()
{
    $this->app->singleton(FootballDataService::class, function ($app) {
        return new FootballDataService(
            apiToken: $app['config']['services']['football-data']['api-token'],
            client: new Client(),
            rateLimiterState: new Psr16RateLimitState(
                cacheKey: 'football-data', 
                cache: $app->make('cache.store'), 
                maxAttempts: 10,            
                decaySeconds: 60,
            )
        );
    });
...
}
```

## Credits
This package uses code from and is greatly inspired by 

* [Forge SDK package](https://github.com/themsaid/forge-sdk) by [Mohammed Said](https://github.com/themsaid) 
* [Working with 3rd party services in Laravel](https://youtu.be/dGWoYZg7VWU) by [Steve McDougall](https://github.com/JustSteveKing) 

## Testing

This package requires PHP8.1. If you don't have this version locally or as default PHP version, then you can use the `bin/develop` helper script. The script is inspired by Laravel Sail, but is much simpler. To use the script you should have Docker installed. It will pull down PHP8.1 for you and allow you to run the testing commands below.

To use the script
```bash
# Enable helper script
chmod +x bin/develop

# Install PHP dependencies
bin/develop composer install

# Run code style formatting
bin/develop format

# Run static analysis
bin/develop analyse

# Run tests
bin/develop test
```

The testing environment uses [guzzlehttp/guzzle](https://github.com/guzzle/guzzle/). I experienced some issues with the stream interface of the [nyholm/psr7](https://github.com/Nyholm/psr7) package when loading the fixture-responses into a ResponseInterface in the testing environment, as the body stream that was created from a local file would not play nice with [halaxa/json-machine](https://github.com/halaxa/json-machine) package. The Nyholm package works well in production and with real responses.

## About
Einar Hansen is a webdeveloper in Oslo, Norway. You'll find more information about me [on my website](https://einarhansen.dev).

## License

The MIT License (MIT).
