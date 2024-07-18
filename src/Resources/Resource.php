<?php

declare(strict_types=1);

namespace EinarHansen\FootballData\Resources;

use EinarHansen\Http\Message\RequestFactory;
use EinarHansen\Http\Resource\Resource as BaseResource;
use Psr\Http\Message\ResponseInterface;

class Resource extends BaseResource
{
    public function attempt(RequestFactory $requestFactory): ResponseInterface|false
    {
        return $this->service()->attempt($requestFactory);
    }

    public function makeRequest(): RequestFactory
    {
        return $this->service()->makeRequest();
    }
}
