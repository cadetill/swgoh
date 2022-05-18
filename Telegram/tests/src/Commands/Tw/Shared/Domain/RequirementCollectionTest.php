<?php

namespace Im\Tests\Commands\Tw\Shared\Domain;

use Im\Commands\Tw\Shared\Domain\RequirementCollection;
use Im\Commands\Tw\Shared\Domain\WrongRequirementDefinition;
use Im\Shared\Infrastructure\StatService;
use Im\Shared\Infrastructure\UnitRepository;

expect()->extend('isSuccesful', function () {
    $this->complain()->toBeTrue();

    return $this;
});
expect()->extend('isNotSuccesful', function () {
    $this->complain()->toBeFalse();

    return $this;
});
expect()->extend('hasSuccesfulReport', function ($report) {
    $this->report()->toContain("\xF0\x9F\x94\xB9 " . $report);

    return $this;
});
expect()->extend('hasNotSuccesfulReport', function ($report) {
    $this->report()->toContain("\xF0\x9F\x94\xBB " . $report);

    return $this;
});

it('should raise an exception on bad definition', function () {
    try {
        new RequirementCollection(
            'bad definition',
            mock(UnitRepository::class)->makePartial(),
            mock(StatService::class)->makePartial()
        );
        expect(true)->toBeFalse();
    } catch (WrongRequirementDefinition $ex) {
        expect($ex->definition())->toEqual('bad definition');
        expect($ex->type())->toEqual(WrongRequirementDefinition::UNKNOWN);
    }
});

it('should be able to check unit stats', function () {
    $complainPlayer    = [ UnitMother::withStats('KUIIL', [ 'Speed', 340 ]) ];
    $notComplainPlayer = [ UnitMother::withStats('KUIIL', [ 'Speed', 339 ]) ];
    $aliasMock         = mock(UnitRepository::class)->expect(
        existAlias: fn($alias) => [ 'kuiil' => true ][$alias] ?? false,
        unitByAlias: fn($alias) => [ 'kuiil' => [ 'baseId' => 'KUIIL', 'name' => 'Kuiil' ] ][$alias],
    );
    $statsMock         = mock(StatService::class)->expect(
        exist: fn($alias) => [ 's' => true ][$alias] ?? false,
        statByAlias: fn($alias) => [ 's' => [ 'key' => 'Speed', 'name' => 'Velocidad', 'percentage' => false ] ][$alias]
    );

    $requirements        = new RequirementCollection('kuiil(s,340)', $aliasMock, $statsMock);
    $successfulResult    = $requirements->checkPlayer($complainPlayer);
    $notSuccessfulResult = $requirements->checkPlayer($notComplainPlayer);

    expect($successfulResult)
        ->isSuccesful()
        ->hasSuccesfulReport('[Kuiil][Velocidad] 340 >= 340');
    expect($notSuccessfulResult)
        ->isNotSuccesful()
        ->hasNotSuccesfulReport('[Kuiil][Velocidad] 339 >= 340');
});

it('should be able to check unit stats with custom comparator', function () {
    $complainPlayer    = [ UnitMother::withStats('COMMANDERAHSOKA', [ 'Speed', 279 ]) ];
    $notComplainPlayer = [ UnitMother::withStats('COMMANDERAHSOKA', [ 'Speed', 280 ]) ];
    $aliasMock         = mock(UnitRepository::class)->expect(
        existAlias: fn($alias) => [ 'cat' => true ][$alias] ?? false,
        unitByAlias: fn($alias) => [
            'cat' => [
                'baseId' => 'COMMANDERAHSOKA',
                'name'   => 'Comandante Ahsoka Tano',
            ],
        ][$alias],
    );
    $statsMock         = mock(StatService::class)->expect(
        exist: fn($alias) => [ 's' => true ][$alias] ?? false,
        statByAlias: fn($alias) => [ 's' => [ 'key' => 'Speed', 'name' => 'Velocidad', 'percentage' => false ] ][$alias]
    );

    $requirements        = new RequirementCollection('cat(s,<,280)', $aliasMock, $statsMock);
    $successfulResult    = $requirements->checkPlayer($complainPlayer);
    $notSuccessfulResult = $requirements->checkPlayer($notComplainPlayer);

    expect($successfulResult)
        ->isSuccesful()
        ->hasSuccesfulReport('[Comandante Ahsoka Tano][Velocidad] 279 &lt; 280');
    expect($notSuccessfulResult)
        ->isNotSuccesful()
        ->hasNotSuccesfulReport('[Comandante Ahsoka Tano][Velocidad] 280 &lt; 280');
});

it('should be able to report properly unit stats with percentage', function () {
    $complainPlayer    = [ UnitMother::withStats('CHEWBACCALEGENDARY', [ 'Tenacity', 1.5 ]) ];
    $notComplainPlayer = [ UnitMother::withStats('CHEWBACCALEGENDARY', [ 'Tenacity', 1.49 ]) ];
    $aliasMock         = mock(UnitRepository::class)->expect(
        existAlias: fn($alias) => [ 'chewie' => true ][$alias] ?? false,
        unitByAlias: fn($alias) => [ 'chewie' => [ 'baseId' => 'CHEWBACCALEGENDARY', 'name' => 'Chewbacca' ] ][$alias],
    );
    $statsMock         = mock(StatService::class)->expect(
        exist: fn($alias) => [ 't' => true ][$alias] ?? false,
        statByAlias: fn($alias) => [ 't' => [ 'key' => 'Tenacity', 'name' => 'Tenacidad', 'percentage' => true ] ][$alias]
    );

    $requirements        = new RequirementCollection('chewie(t,150)', $aliasMock, $statsMock);
    $successfulResult    = $requirements->checkPlayer($complainPlayer);
    $notSuccessfulResult = $requirements->checkPlayer($notComplainPlayer);

    expect($successfulResult)
        ->isSuccesful()
        ->hasSuccesfulReport('[Chewbacca][Tenacidad] 150.00 % >= 150 %');
    expect($notSuccessfulResult)
        ->isNotSuccesful()
        ->hasNotSuccesfulReport('[Chewbacca][Tenacidad] 149.00 % >= 150 %');
});

it('should be able to check unit relic', function () {
    $complainPlayer    = [ UnitMother::withRelic('KUIIL', 5) ];
    $notComplainPlayer = [ UnitMother::withRelic('KUIIL', 4) ];
    $aliasMock         = mock(UnitRepository::class)->expect(
        existAlias: fn($alias) => [ 'kuiil' => true ][$alias] ?? false,
        unitByAlias: fn($alias) => [ 'kuiil' => [ 'baseId' => 'KUIIL', 'name' => 'Kuiil' ] ][$alias],
    );
    $statsMock         = mock(StatService::class)->makePartial();

    $requirements        = new RequirementCollection('kuiil(r,5)', $aliasMock, $statsMock);
    $successfulResult    = $requirements->checkPlayer($complainPlayer);
    $notSuccessfulResult = $requirements->checkPlayer($notComplainPlayer);

    expect($successfulResult)
        ->isSuccesful()
        ->hasSuccesfulReport('[Kuiil][Reliquia] 5 >= 5');
    expect($notSuccessfulResult)
        ->isNotSuccesful()
        ->hasNotSuccesfulReport('[Kuiil][Reliquia] 4 >= 5');
});

it('should be able to check multiple restrictions', function () {
    $complainPlayer    = [
        UnitMother::withStats('DAKA', [ 'Health', 150000 ]),
        UnitMother::withStats('MOTHERTALZIN', [ 'Speed', 280 ]),
    ];
    $notComplainPlayer = [
        UnitMother::withStats('DAKA', [ 'Health', 149999 ]),
        UnitMother::withStats('MOTHERTALZIN', [ 'Speed', 280 ]),
    ];
    $aliasMock         = mock(UnitRepository::class)->expect(
        existAlias: fn($alias) => [
                'daka'   => true,
                'talzin' => true,
            ][$alias] ?? false,
        unitByAlias: fn($alias) => [
            'daka'   => [ 'baseId' => 'DAKA', 'name' => 'Vieja Daka' ],
            'talzin' => [ 'baseId' => 'MOTHERTALZIN', 'name' => 'Madre Talzin' ],
        ][$alias],
    );
    $statsMock         = mock(StatService::class)->expect(
        exist: fn($alias) => [ 'h' => true, 's' => true ][$alias] ?? false,
        statByAlias: fn($alias) => [
            'h' => [ 'key' => 'Health', 'name' => 'Salud', 'percentage' => false ],
            's' => [ 'key' => 'Speed', 'name' => 'Velocidad', 'percentage' => false ],
        ][$alias]
    );

    $requirements        = new RequirementCollection('daka(h,150000),talzin(s,280)', $aliasMock, $statsMock);
    $successfulResult    = $requirements->checkPlayer($complainPlayer);
    $notSuccessfulResult = $requirements->checkPlayer($notComplainPlayer);

    expect($successfulResult)
        ->isSuccesful()
        ->hasSuccesfulReport('[Vieja Daka][Salud] 150000 >= 150000')
        ->hasSuccesfulReport('[Madre Talzin][Velocidad] 280 >= 280');
    expect($notSuccessfulResult)
        ->isNotSuccesful()
        ->hasNotSuccesfulReport('[Vieja Daka][Salud] 149999 >= 150000')
        ->hasSuccesfulReport('[Madre Talzin][Velocidad] 280 >= 280');
});

it('should be able to compare stats between units', function () {
    // (((351 + 20) * 0.8) - 20) = 276,8
    $complainPlayer    = [
        UnitMother::withStats('ADMIRALPIETT', [ 'Speed', 351 ]),
        UnitMother::withStats('COLONELSTARCK', [ 'Speed', 277 ]),
    ];
    $notComplainPlayer = [
        UnitMother::withStats('ADMIRALPIETT', [ 'Speed', 351 ]),
        UnitMother::withStats('COLONELSTARCK', [ 'Speed', 276 ]),
    ];
    $aliasMock         = mock(UnitRepository::class)->expect(
        existAlias: fn($alias) => [
                'piett'  => true,
                'starck' => true,
            ][$alias] ?? false,
        unitByAlias: fn($alias) => [
            'piett'  => [ 'baseId' => 'ADMIRALPIETT', 'name' => 'Almirante Piett' ],
            'starck' => [ 'baseId' => 'COLONELSTARCK', 'name' => 'Coronel Starck' ],
        ][$alias],
    );
    $statsMock         = mock(StatService::class)->expect(
        exist: fn($alias) => [ 'h' => true, 's' => true ][$alias] ?? false,
        statByAlias: fn($alias) => [
            's' => [ 'key' => 'Speed', 'name' => 'Velocidad', 'percentage' => false ],
        ][$alias]
    );

    $requirements        = new RequirementCollection('starck(s,piett,>=,((r+20)*0.8)-20)', $aliasMock, $statsMock);
    $successfulResult    = $requirements->checkPlayer($complainPlayer);
    $notSuccessfulResult = $requirements->checkPlayer($notComplainPlayer);

    expect($successfulResult)
        ->isSuccesful()
        ->hasSuccesfulReport(
            '[Coronel Starck][Velocidad] 277 >= 276.80 [(([Almirante Piett][Velocidad][351]+20)*0.8)-20]'
        );
    expect($notSuccessfulResult)
        ->isNotSuccesful()
        ->hasNotSuccesfulReport(
            '[Coronel Starck][Velocidad] 276 >= 276.80 [(([Almirante Piett][Velocidad][351]+20)*0.8)-20]'
        );
});

it('should be able to check team order', function () {
    // BB8 > IMPERIALPROBEDROID > T3_M4 > IG88 > GRIEVOUS
    $complainPlayer    = [
        UnitMother::withStats('GRIEVOUS', [ 'Speed', 1 ]),
        UnitMother::withStats('IG88', [ 'Speed', 2 ]),
        UnitMother::withStats('T3_M4', [ 'Speed', 3 ]),
        UnitMother::withStats('IMPERIALPROBEDROID', [ 'Speed', 4 ]),
        UnitMother::withStats('BB8', [ 'Speed', 5 ]),
    ];
    $notComplainPlayer = [
        UnitMother::withStats('GRIEVOUS', [ 'Speed', 1 ]),
        UnitMother::withStats('IG88', [ 'Speed', 2 ]),
        UnitMother::withStats('T3_M4', [ 'Speed', 3 ]),
        UnitMother::withStats('IMPERIALPROBEDROID', [ 'Speed', 4 ]),
        UnitMother::withStats('BB8', [ 'Speed', 1 ]),
    ];
    $aliasMock         = mock(UnitRepository::class)->expect(
        existAlias: fn($alias) => [
            'bb8'  => true,
            'ipd'  => true,
            't3'   => true,
            'ig88' => true,
            'gg'   => true,
        ][$alias] ?? false,
        unitByAlias: fn($alias) => [
            'bb8'  => [ 'baseId' => 'BB8', 'name' => 'BB8' ],
            'ipd'  => [ 'baseId' => 'IMPERIALPROBEDROID', 'name' => 'Droide Sonda Imperial' ],
            't3'   => [ 'baseId' => 'T3_M4', 'name' => 'T3-M4' ],
            'ig88' => [ 'baseId' => 'IG88', 'name' => 'Ig 88' ],
            'gg'   => [ 'baseId' => 'GRIEVOUS', 'name' => 'General Grievous' ],
        ][$alias],
    );
    $statsMock         = mock(StatService::class)->makePartial();

    $requirements        = new RequirementCollection('order(bb8,ipd,t3,ig88,gg)', $aliasMock, $statsMock);
    $successfulResult    = $requirements->checkPlayer($complainPlayer);
    $notSuccessfulResult = $requirements->checkPlayer($notComplainPlayer);

    expect($successfulResult)
        ->isSuccesful()
        ->hasSuccesfulReport('BB8(5) > Droide Sonda Imperial(4) > T3-M4(3) > Ig 88(2) > General Grievous(1)');
    expect($notSuccessfulResult)
        ->isNotSuccesful()
        ->hasNotSuccesfulReport('BB8(1) > Droide Sonda Imperial(4) > T3-M4(3) > Ig 88(2) > General Grievous(1)');
});

it('should be able to check unit skills', function () {
    $complainPlayer    = [ UnitMother::withSkills('MARAJADE', [ 'uniqueskill_MARAJADE01', 'Mano del emperador', 8, 8 ]) ];
    $notComplainPlayer = [ UnitMother::withSkills('MARAJADE', [ 'uniqueskill_MARAJADE01', 'Mano del emperador', 7, 8 ]) ];
    $aliasMock         = mock(UnitRepository::class)->expect(
        existAlias: fn($alias) => [
                'mara'  => true,
            ][$alias] ?? false,
        unitByAlias: fn($alias) => [
            'mara'  => [ 'baseId' => 'MARAJADE', 'name' => 'Mara Jade, la mano del Emperador' ],
        ][$alias],
    );
    $statsMock         = mock(StatService::class)->makePartial();

    $requirements        = new RequirementCollection('mara(sk,u)', $aliasMock, $statsMock);
    $successfulResult    = $requirements->checkPlayer($complainPlayer);
    $notSuccessfulResult = $requirements->checkPlayer($notComplainPlayer);

    expect($successfulResult)
        ->isSuccesful()
        ->hasSuccesfulReport('[Mara Jade, la mano del Emperador][Mano del emperador]');
    expect($notSuccessfulResult)
        ->isNotSuccesful()
        ->hasNotSuccesfulReport('[Mara Jade, la mano del Emperador][Mano del emperador]');
});
