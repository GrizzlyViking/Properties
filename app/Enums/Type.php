<?php

namespace App\Enums;

enum Type: string
{
    case CORPORATION='Corporation';
    case BUILDING='Building';
    case PROPERTY='Property';
    case TENANCY_PERIOD='Tenancy Period';
    case TENANT='Tenant';

    public function height(): int
    {
        return match ($this) {
            self::CORPORATION => 0,
            self::BUILDING => 1,
            self::PROPERTY => 2,
            self::TENANCY_PERIOD => 3,
            self::TENANT => 4,
        };
    }
}
