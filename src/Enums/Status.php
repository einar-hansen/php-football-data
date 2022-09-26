<?php

declare(strict_types=1);

namespace EinarHansen\FootballData\Enums;

enum Status: string
{
    case CANCELLED = 'CANCELLED';
    case FINISHED = 'FINISHED';
    case IN_PLAY = 'IN_PLAY';
    case LIVE = 'LIVE';
    case PAUSED = 'PAUSED';
    case POSTPONED = 'POSTPONED';
    case SCHEDULED = 'SCHEDULED';
    case SUSPENDED = 'SUSPENDED';
    case TIMED = 'TIMED';
}
