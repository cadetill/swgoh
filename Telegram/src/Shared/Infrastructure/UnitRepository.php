<?php

namespace Im\Shared\Infrastructure;

interface UnitRepository
{
    public function existAlias(string $alias): bool;
    public function unitByAlias(string $alias): array;
}
