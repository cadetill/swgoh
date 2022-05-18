<?php

namespace Im\Commands\Tw\Shared\Domain;

use Im\Shared\Exception\ImException;

class WrongRequirementDefinition extends ImException
{
    public const UNKNOWN          = 1;
    public const LEFT_SIDE        = 2;
    public const STAT_TYPE        = 3;
    public const RELIC_TYPE       = 4;
    public const REFERAL_TYPE     = 5;
    public const ALIAS_TYPE       = 6;
    public const SKILL_TYPE       = 7;
    public const SKILL_ALIAS_TYPE = 8;
    public const EXTRA            = 9;

    private string $definition;
    private int    $type;
    private ?string $extra;

    public function __construct(string $definition, int $type, string $extra = null)
    {
        $this->definition = $definition;
        $this->type       = $type;
        $this->extra      = $extra;

        parent::__construct();
    }

    public static function extra(string $definition, ?string $extra)
    {
        return new self($definition, self::EXTRA, $extra);
    }

    public function errorCode(): string
    {
        return 'wrong_requirement_definition';
    }

    protected function errorMessage(): string
    {
        return sprintf(
            'La definición <b>%s</b> no es correcta.%s',
            $this->definition,
            $this->typeMessage()
        );
    }

    public static function unknown(string $definition): self
    {
        return new self($definition, self::UNKNOWN);
    }

    public static function leftSide(string $definition, string $leftSide)
    {
        return new self($definition, self::LEFT_SIDE, $leftSide);
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
        return new self($definition, self::ALIAS_TYPE, $alias);
    }

    public static function skill(string $definition)
    {
        return new self($definition, self::SKILL_TYPE);
    }

    public static function skillAlias(string $definition, $skillAlias)
    {
        return new self($definition, self::SKILL_ALIAS_TYPE, $skillAlias);
    }

    public function definition(): string
    {
        return $this->definition;
    }

    public function type(): int
    {
        return $this->type;
    }

    private function typeMessage()
    {
        $typeMessages = [
            self::UNKNOWN          => '',
            self::LEFT_SIDE        => sprintf("\n<b>%s</b> no es un alias válido ni un valor válido", $this->extra),
            self::STAT_TYPE        => '',
            self::RELIC_TYPE       => '',
            self::REFERAL_TYPE     => '',
            self::ALIAS_TYPE       => sprintf("\n<b>%s</b> no es un alias válido", $this->extra),
            self::SKILL_TYPE       => '',
            self::SKILL_ALIAS_TYPE => sprintf("\n<b>%s</b> no es un alias de habilidad válido", $this->extra),
            self::EXTRA            => sprintf("\n<b>%s</b> no es un alias de estadística válido", $this->extra),
        ];

        return $typeMessages[$this->type];
    }
}
