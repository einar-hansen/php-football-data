<?php

declare(strict_types=1);

namespace EinarHansen\FootballData\Resources;

use EinarHansen\FootballData\Data\Area;
use EinarHansen\FootballData\Data\Competition;
use EinarHansen\FootballData\Factories\AreaFactory;

class AreaResource extends Resource
{
    /**
     * List all available areas.
     *
     * No filters are available for this endpoint.
     *
     * @return iterable<int, Area>|false
     */
    public function all(): iterable|false
    {
        $response = $this->attempt(
            $this->makeRequest()
                ->withPath(
                    path: '/v4/areas'
                )
        );
        if (! $response) {
            return false;
        }

        return $this->makeDataCollection(
            response: $response,
            factory: AreaFactory::class,
            pointer: '/areas'
        );
    }

    /**
     * List one particular area.
     */
    public function find(int $areaId): Area|false|null
    {
        $response = $this->attempt(
            $this->makeRequest()
                ->withPath(
                    path: "/v4/areas/{$areaId}"
                )
        );
        if (! $response) {
            return false;
        }

        return $this->makeData(
            response: $response,
            factory: AreaFactory::class
        );
    }

    /**
     * List all competitions in an area.
     *
     * @return iterable<int, Competition>|false
     */
    public function competitions(Area|int $areaId): iterable|false
    {
        if ($areaId instanceof Area) {
            $areaId = $areaId->id;
        }

        return $this->service()
            ->competitions()
            ->all(areaIds: $areaId);
    }
}
