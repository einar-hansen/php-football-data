<?php

declare(strict_types=1);

namespace EinarHansen\FootballData\Enums;

enum Venue: string
{
    case HOME = 'HOME';
    case AWAY = 'AWAY';
}
