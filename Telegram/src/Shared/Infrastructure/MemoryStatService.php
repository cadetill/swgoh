<?php

namespace Im\Shared\Infrastructure;

use TData;
use TStats;

class MemoryStatService implements StatService
{
    private TStats $stats;

    public function __construct(TData $data)
    {
        $this->stats = new TStats([ 'a b', 'c' ], $data);
    }

    public function exist(string $alias): bool
    {
        return TStats::exists($alias);
    }

    public function statByAlias(string $alias): array
    {
        return [
            'key'        => TStats::crinoloAliasFromPre($alias),
            'name'       => ucwords($this->stats->getDescHability($alias)),
            'percentage' => TStats::isPercentual($alias),
        ];
    }
}
