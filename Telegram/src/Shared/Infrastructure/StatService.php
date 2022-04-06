<?php

namespace Im\Shared\Infrastructure;

interface StatService
{
    public function exist(string $alias): bool;
    public function statByAlias(string $alias): array;
}
