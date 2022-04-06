<?php

namespace Im\Commands\Tw\Shared\Domain;

use Exception;

class WrongRequirementDefinition extends Exception
{
    public const UNKNOWN          = 1;
    public const LEFT_SIDE        = 2;
    public const STAT_TYPE        = 3;
    public const RELIC_TYPE       = 4;
    public const REFERAL_TYPE     = 5;
    public const ALIAS_TYPE       = 6;
    public const SKILL_TYPE       = 7;
    const        SKILL_ALIAS_TYPE = 8;

    private string $definition;
    private int    $type;

    public function __construct(string $definition, int $type)
    {
        parent::__construct($definition);
        $this->definition = $definition;
        $this->type       = $type;
    }

    public static function unknown(string $definition): self
    {
        return new self($definition, self::UNKNOWN);
    }

    public static function leftSide(string $definition, string $leftSide)
    {
        return new self($definition, self::LEFT_SIDE);
    }

    public static function statType(string $definition)
    {
        return new self($definition, self::STAT_TYPE);
    }

    public static function relic(string $definition)
    {
        return new self($definition, self::RELIC_TYPE);
    }

    public static function referalType(string $definition)
    {
        return new self($definition, self::REFERAL_TYPE);
    }

    public static function alias(string $definition, string $alias)
    {
        return new self($definition, self::ALIAS_TYPE);
    }

    public static function skill(string $definition)
    {
        return new self($definition, self::SKILL_TYPE);
    }

    public static function skillAlias(string $definition, $skillAlias)
    {
        return new self($definition, self::SKILL_ALIAS_TYPE);
    }

    public function definition(): string
    {
        return $this->definition;
    }

    public function type(): int
    {
        return $this->type;
    }
}
