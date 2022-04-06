<?php

namespace Im\Tests\Commands\Tw\Shared\Domain;

class PlayerMother
{
    public static function withRoster(array ...$units)
    {
        return [
            "roster" => [
                ...$units
            ],
        ];
    }
}
