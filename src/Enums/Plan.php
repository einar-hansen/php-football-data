<?php

declare(strict_types=1);

namespace EinarHansen\FootballData\Enums;

enum Plan: string
{
    case TIER_ONE = 'TIER_ONE';
    case TIER_TWO = 'TIER_TWO';
    case TIER_THREE = 'TIER_THREE';
    case TIER_FOUR = 'TIER_FOUR';
}
