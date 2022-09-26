<?php

namespace EinarHansen\FootballData\Tests\Fixtures;

use EinarHansen\Http\Support\ResponseSerializer;
use Psr\Http\Message\ResponseInterface;

trait HasResponseFixtures
{
    public function loadResponseFixture(string $filename): ResponseInterface
    {
        return (new ResponseSerializer())->get(
            __DIR__.'/FootballData/'.$filename
        );
    }
}
