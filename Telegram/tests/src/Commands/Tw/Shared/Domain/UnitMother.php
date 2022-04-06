<?php

namespace Im\Tests\Commands\Tw\Shared\Domain;

class UnitMother
{
    public static function withStats(string $unitId, array $stats)
    {
        [ $statName, $value ] = $stats;

        return [
            "defId" => $unitId,
            "stats" => [
                "final" => [
                    $statName => $value,
                ],
            ],
        ];
    }

    public static function withRelic(string $unitId, int $value)
    {
        return [
            "defId" => $unitId,
            "relic" => [ "currentTier" => $value + 2 ],
        ];
    }

    public static function withSkills(string $unitId, array $skills)
    {
        [ $skillId, $skillName, $skillTier, $skillTierMax ] = $skills;

        return [
            "defId"  => $unitId,
            "skills" => [
                [
                    "id"      => $skillId,
                    "nameKey" => $skillName,
                    "tier"    => $skillTier,
                    "tiers"   => $skillTierMax,
                ],
            ],
        ];
    }
}
