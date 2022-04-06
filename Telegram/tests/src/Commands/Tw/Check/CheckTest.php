<?php

namespace Im\Tests\Commands\Tw\Check;

use Im\Commands\Tw\Check\Check;
use Im\Commands\Tw\Check\CheckRepository;
use Im\Commands\Tw\Check\CheckRequest;
use Im\Commands\Tw\Check\CheckResponse;
use Im\Shared\Domain\AllyCode;
use Im\Shared\Domain\GuildId;
use Im\Shared\Domain\User;
use Im\Shared\Infrastructure\UnitRepository;

it('should return a response', function () {
    $command = $command = new Check(
        mock(CheckRepository::class),
        mock(UnitRepository::class)
    );

    $guildId      = new GuildId('G2548116260');
    $allyCode     = new AllyCode(336771469);
    $checkRequest = new CheckRequest(new User($allyCode, $guildId));
    $response     = $command->execute($checkRequest);

    expect($response)->toBeInstanceOf(CheckResponse::class);
});
