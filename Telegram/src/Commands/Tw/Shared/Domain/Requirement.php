<?php

namespace Im\Commands\Tw\Shared\Domain;

use Im\Shared\Infrastructure\StatService;
use Im\Shared\Infrastructure\UnitRepository;

class Requirement
{
    public const ORDER_TYPE_ALIAS  = 'order';
    public const RELIC_TYPE_ALIAS  = 'r';
    public const SKILL_TYPE_ALIAS  = 'sk';
    public const CUSTOM_TYPE_ALIAS = 'c';

    public const ORDER_TYPE   = 1;
    public const STAT_TYPE    = 2;
    public const RELIC_TYPE   = 3;
    public const REFERAL_TYPE = 4;
    public const SKILL_TYPE   = 5;
    public const CUSTOM_TYPE  = 6;

    private int            $type;
    private string         $definition;
    private UnitRepository $aliasRepository;
    private array          $unit;
    private array          $stat;
    private StatService    $statService;
    private array          $referalUnit;
    private string         $operation;
    private string         $report;
    private string         $targetOperation;
    private array          $unitsOrder;
    private string         $skillId;

    private array  $referalUnits;
    private string $leftSideOperation;
    private string $rightSideOperation;
    private string $operator;

    public function __construct(string $definition, UnitRepository $aliasRepository, StatService $statService)
    {
        $this->definition      = $definition;
        $this->aliasRepository = $aliasRepository;
        $this->statService     = $statService;

        $this->guard();
    }

    public function checkPlayer(array $roster)
    {
        switch ($this->type) {
            case self::SKILL_TYPE:
                $findSkill  = function ($unit, $skillId) {
                    foreach ($unit['skills'] as $skill) {
                        if ($skill['id'] === $skillId) {
                            return $skill;
                        }
                    }

                    return null;
                };
                $playerUnit = $this->playerUnit($roster, $this->unit['baseId']);
                if (!$playerUnit) {
                    $complain = false;
                    $report   = str_replace(':skill', '-', $this->report);
                    break;
                }
                $playerUnitSkill = $findSkill($playerUnit, $this->skillId);
                $complain        = $playerUnitSkill['tier'] === $playerUnitSkill['tiers'];
                $report          = str_replace(':skill', $playerUnitSkill['nameKey'], $this->report);
                break;
            case self::ORDER_TYPE:
                $lastUnitSpeed = PHP_INT_MAX;
                $fail          = false;
                $playerSpeeds  = [];
                foreach ($this->unitsOrder as $unit) {
                    $unitId     = $unit['baseId'];
                    $playerUnit = $this->playerUnit($roster, $unitId);
                    if (!$playerUnit) {
                        $playerSpeeds[] = '-';
                        $fail           = true;
                    } else {
                        $playerUnitSpeed = $playerUnit['stats']['final']['Speed'];
                        $playerSpeeds[]  = $playerUnitSpeed;
                        if ($playerUnitSpeed < $lastUnitSpeed) {
                            $lastUnitSpeed = $playerUnitSpeed;
                        } else {
                            $fail = true;
                        }
                    }
                }

                $complain = !$fail;
                $report   = sprintf($this->report, ...$playerSpeeds);
                break;
            case self::STAT_TYPE:
                $playerUnit = $this->playerUnit($roster, $this->unit['baseId']);
                if (!$playerUnit) {
                    $complain = false;
                    $report   = str_replace(':value', '-', $this->report);
                    break;
                }
                $value = $this->statValue($playerUnit, $this->stat);
                if ($this->stat['percentage']) {
                    $value = $value * 100;
                }
                $complainExpression = str_replace(':value', $value, $this->operation);
                $complain           = $this->evalExpression($complainExpression);
                if ($this->stat['percentage']) {
                    $value = number_format($value, 2);
                }
                $report = str_replace(':value', $value, $this->report);
                break;
            case self::RELIC_TYPE:
                $playerUnit = $this->playerUnit($roster, $this->unit['baseId']);
                if (!$playerUnit) {
                    $complain = false;
                    $report   = str_replace(':value', '-', $this->report);
                    break;
                }
                $value              = $playerUnit['relic']['currentTier'] - 2;
                $complainExpression = str_replace(':value', $value, $this->operation);
                $complain           = $this->evalExpression($complainExpression);
                $report             = str_replace(':value', $value, $this->report);

                break;
            case self::REFERAL_TYPE:
                $playerUnit = $this->playerUnit($roster, $this->unit['baseId']);
                if (!$playerUnit) {
                    $complain = false;
                    $report   = str_replace(
                        [ ':value', ':target', ':referal' ],
                        [ '-', '-', '-' ],
                        $this->report
                    );
                    break;
                }
                $value         = $this->statValue($playerUnit, $this->stat);
                $playerReferal = $this->playerUnit($roster, $this->referalUnit['baseId']);
                if (!$playerReferal) {
                    $complain = false;
                    $report   = str_replace(
                        [ ':value', ':target', ':referal' ],
                        [ $value, '-', '-' ],
                        $this->report
                    );
                    break;
                }
                $referalValue = $this->statValue($playerReferal, $this->stat);

                $targetExpression = str_replace(':referal', $referalValue, $this->targetOperation);
                $target           = $this->evalExpression($targetExpression);

                $complainExpression = str_replace(
                    [ ':value', ':target', ],
                    [ $value, $target ],
                    $this->operation
                );
                $complain           = $this->evalExpression($complainExpression);
                $report             = str_replace(
                    [ ':value', ':target', ':referal' ],
                    [ $value, number_format($target, 2), $referalValue ],
                    $this->report
                );
                break;
            case self::CUSTOM_TYPE:
                $playerUnit         = $this->playerUnit($roster, $this->unit['baseId']);
                $leftSideOperation  = $this->leftSideOperation;
                $rightSideOperation = $this->rightSideOperation;
                $report             = $this->report;
                $targetOperation    = $this->targetOperation;
                foreach ($this->referalUnits as $referalUnit) {
                    [ $unitAlias, $statAlias ] = explode('.', $referalUnit);
                    $unit = $this->aliasRepository->unitByAlias($unitAlias);
                    $stat = $this->statService->statByAlias($statAlias);

                    $playerReferalUnit      = $this->playerUnit($roster, $unit['baseId']);
                    $playerReferalStatValue = $this->statValue($playerReferalUnit, $stat);

                    $leftSideOperation     = str_replace(
                        $referalUnit,
                        $playerReferalStatValue,
                        $leftSideOperation
                    );
                    $rightSideOperation    = str_replace(
                        $referalUnit,
                        $playerReferalStatValue,
                        $rightSideOperation
                    );
                    $targetOperation = str_replace(
                        $referalUnit,
                        $playerReferalStatValue,
                        $targetOperation
                    );
                    $report                = str_replace(
                        $referalUnit,
                        sprintf(
                            '[%s][%s][%s]',
                            $unit['name'],
                            $stat['name'],
                            $playerReferalStatValue
                        ),
                        $report
                    );
                }
                $complain = $this->evalExpression($targetOperation);
                $report   = str_replace(
                    [ ':leftSide', ':rightSide' ],
                    [
                        $this->evalExpression($leftSideOperation),
                        $this->evalExpression($rightSideOperation),
                    ],
                    $report
                );
                break;
        }

        return new RequirementResult($complain, $report);
    }

    public function unitIds(): array
    {
        switch ($this->type) {
            case self::ORDER_TYPE:
                return array_column($this->unitsOrder, 'baseId');
            case self::REFERAL_TYPE:
                return [ $this->unit['baseId'], $this->referalUnit['baseId'] ];
            case self::STAT_TYPE:
            case self::RELIC_TYPE:
            case self::SKILL_TYPE:
                return [ $this->unit['baseId'] ];
        }

        return [];
    }

    public function show()
    {
        switch ($this->type) {
            case self::ORDER_TYPE:
                $show = str_replace('(%s)', '', $this->report);
                break;
            case self::REFERAL_TYPE:
                $show = str_replace([ ':value ', ':target ', '[:referal]' ], '', $this->report);
                break;
            case self::STAT_TYPE:
            case self::RELIC_TYPE:
                $show = str_replace(':value ', '', $this->report);
                break;
            case self::SKILL_TYPE:
                $show = str_replace(':skill', $this->skillId, $this->report);
                break;
            default:
                $show = '';
                break;
        }

        return '<b>' . str_replace('<', '&lt;', $this->definition) . "</b>\n" . str_replace('<', '&lt;', $show);
    }

    private function guard()
    {
        $firstBracketIndex = strpos($this->definition, '(');
        if ($firstBracketIndex === false) {
            throw WrongRequirementDefinition::unknown($this->definition);
        }
        $leftSide  = substr($this->definition, 0, $firstBracketIndex);
        $rightSide = substr($this->definition, $firstBracketIndex + 1, -1);

        if ($leftSide === self::ORDER_TYPE_ALIAS) {
            $this->guardOrder($rightSide);

            return;
        }

        if (!$this->aliasRepository->existAlias($leftSide)) {
            throw WrongRequirementDefinition::leftSide($this->definition, $leftSide);
        }

        $defaults = [ null, null, null, null ];
        [ $first, $second, $third, $fourth ] = explode(',', $rightSide) + $defaults;
        if ($first === self::CUSTOM_TYPE_ALIAS) {
            $this->guardCustomType($leftSide, [ $first, $second, $third, $fourth ]);

            return;
        }

        if ($first === self::SKILL_TYPE_ALIAS) {
            $this->guardSkillType($leftSide, [ $first, $second, $third, $fourth ]);

            return;
        }

        if ($first === self::RELIC_TYPE_ALIAS) {
            $this->guardRelicType($leftSide, [ $first, $second, $third, $fourth ]);

            return;
        }

        if ($this->statService->exist($first)) {
            if ($this->aliasRepository->existAlias($second)) {
                $this->guardReferalStats($leftSide, [ $first, $second, $third, $fourth ]);

                return;
            }

            $this->guardStatType($leftSide, [ $first, $second, $third, $fourth ]);

            return;
        }

        throw WrongRequirementDefinition::extra($this->definition, $first);
    }

    private function playerUnit(array $roster, $baseId)
    {
        foreach ($roster as $playerUnit) {
            if ($playerUnit['defId'] === $baseId) {
                return $playerUnit;
            }
        }

        return false;
    }

    private function guardStatType($unitAlias, array $params)
    {
        [ $statAlias, $targetOrOperator, $targetOrNull, $null ] = $params;

        if ($this->isOperator($targetOrOperator)) {
            $operator = $targetOrOperator === '=' ? '==' : $targetOrOperator;
            $target   = $targetOrNull;
        } else {
            $operator = '>=';
            $target   = $targetOrOperator;
            if (!is_null($targetOrNull) || !is_null($null)) {
                throw WrongRequirementDefinition::statType($this->definition);
            }
        }

        $this->type      = self::STAT_TYPE;
        $this->stat      = $this->statService->statByAlias($statAlias);
        $this->operation = sprintf(
            ':value %s %s',
            $operator,
            $target,
        );
        $this->unit      = $this->aliasRepository->unitByAlias($unitAlias);
        $this->report    = sprintf(
            '[%s][%s] :value %s%s %s%s',
            $this->unit['name'],
            $this->stat['name'],
            $this->stat['percentage'] ? '% ' : '',
            $operator,
            $target,
            $this->stat['percentage'] ? ' %' : ''
        );
    }

    private function guardRelicType($unitAlias, array $params)
    {
        [ $relicAlias, $relicTarget, $null1, $null2 ] = $params;
        if (!is_null($null1) || !is_null($null2)) {
            throw WrongRequirementDefinition::relic($this->definition);
        }
        $this->type      = self::RELIC_TYPE;
        $this->unit      = $this->aliasRepository->unitByAlias($unitAlias);
        $this->operation = sprintf(':value >= %s', $relicTarget);
        // ToDo
        $typeAlias    = 'Reliquia';
        $this->report = sprintf(
            '[%s][%s] :value >= %s',
            $this->unit['name'],
            $typeAlias,
            $relicTarget
        );
    }

    private function isOperator(string $operator)
    {
        return in_array($operator, [ '>', '>=', '<', '<=', '=' ]);
    }

    private function guardReferalStats(string $unitAlias, array $params)
    {
        [ $statAlias, $referalUnitAlias, $operator, $operation ] = $params;
        if (!$this->isOperator($operator)) {
            throw WrongRequirementDefinition::referalType($this->definition);
        }
        if (!$this->aliasRepository->existAlias($referalUnitAlias)) {
            throw WrongRequirementDefinition::alias($this->definition, $referalUnitAlias);
        }
        $operator = $operator === '=' ? '==' : $operator;

        $this->type        = self::REFERAL_TYPE;
        $this->stat        = $this->statService->statByAlias($statAlias);
        $this->unit        = $this->aliasRepository->unitByAlias($unitAlias);
        $this->referalUnit = $this->aliasRepository->unitByAlias($referalUnitAlias);

        $targetOperation       = str_replace('r', ':referal', $operation);
        $this->targetOperation = $targetOperation;
        $this->operation       = sprintf(':value %s :target', $operator);

        $reportReferal = sprintf('[%s][%s][:referal]', $this->referalUnit['name'], $this->stat['name']);
        $this->report  = sprintf(
            '[%s][%s] :value %s :target [%s]',
            $this->unit['name'],
            $this->stat['name'],
            $operator,
            str_replace('r', $reportReferal, $operation)
        );
    }

    private function evalExpression(string $expression)
    {
        $evalExpression = sprintf('return %s;', $expression);

        return eval($evalExpression);
    }

    private function guardOrder(string $unitAliases)
    {
        $unitAliasesOrder = explode(',', $unitAliases);
        $units            = [];
        $report           = [];
        foreach ($unitAliasesOrder as $alias) {
            if (!$this->aliasRepository->existAlias($alias)) {
                throw WrongRequirementDefinition::alias($this->definition, $alias);
            }
            $unit     = $this->aliasRepository->unitByAlias($alias);
            $units[]  = $unit;
            $report[] = $unit['name'];
        }

        $this->type       = self::ORDER_TYPE;
        $this->report     = join('(%s) > ', $report) . '(%s)';
        $this->unitsOrder = $units;
    }

    private function guardSkillType(string $unitAlias, array $params)
    {
        [ $skillType, $skillAlias, $null1, $null2 ] = $params;
        if (!is_null($null1) || !is_null($null2)) {
            throw WrongRequirementDefinition::skill($this->definition);
        }

        $skillPrefix = substr($skillAlias, 0, 1);
        if (!in_array($skillPrefix, [ 'b', 's', 'l', 'u' ])) {
            throw WrongRequirementDefinition::skillAlias($this->definition, $skillAlias);
        }

        $skillDefaultIndexes = [
            'b' => 1,
            's' => 1,
            'l' => '',
            'u' => 1,
        ];
        $skillDefaultIndexPrefix = [
            'b' => '0',
            's' => '0',
            'l' => '',
            'u' => '0',
        ];
        $skillIndex          = strlen($skillAlias) === 2
            ? substr($skillAlias, 1, 1)
            : $skillDefaultIndexes[$skillPrefix];

        $this->unit = $this->aliasRepository->unitByAlias($unitAlias);

        $skillAliases = [
            'b' => 'basic',
            's' => 'special',
            'l' => 'leader',
            'u' => 'unique',
        ];

        $this->skillId = sprintf(
            '%sskill_%s%s%s',
            $skillAliases[$skillPrefix],
            $this->unit['baseId'],
            $skillDefaultIndexPrefix[$skillPrefix],
            $skillIndex
        );
        $this->report  = sprintf('[%s][:skill]', $this->unit['name']);
        $this->type    = self::SKILL_TYPE;
    }

    private function statValue(array $playerUnit, array $stat)
    {
        $statKeys   = is_array($stat['key']) ? $stat['key'] : [ $stat['key'] ];
        $finalStats = $playerUnit['stats']['final'];
        $value      = array_reduce(
            $statKeys,
            function ($carry, $statKey) use ($finalStats) {
                return $carry + $finalStats[$statKey];
            },
            0
        );

        return $value;
    }

    private function guardCustomType(string $unitAlias, array $params)
    {
        [ $prefix, $formula, $null1, $null2 ] = $params;
        if (!is_null($null1) || !is_null($null2)) {
            throw WrongRequirementDefinition::custom($this->definition);
        }

        $this->type         = self::CUSTOM_TYPE;
        $this->unit         = $this->aliasRepository->unitByAlias($unitAlias);
        $this->referalUnits = [];

        $pregMAOutput = [];
        preg_match_all('/[a-zA-Z0-9]+\.[a-zA-Z]+/i', $formula, $pregMAOutput);
        foreach ($pregMAOutput[0] as $match) {
            [ $unitAlias, $statAlias ] = explode('.', $match);
            if (!$this->aliasRepository->existAlias($unitAlias)) {
                throw WrongRequirementDefinition::alias($this->definition, $unitAlias);
            }
            if (!$this->statService->exist($statAlias)) {
                throw WrongRequirementDefinition::extra($this->definition, $statAlias);
            }
            $this->referalUnits[] = $match;
        }

        [ $leftSideOperation, $rightSideOperation ] = preg_split('/[<>]=?/i', $formula);
        $this->leftSideOperation  = trim($leftSideOperation);
        $this->rightSideOperation = trim($rightSideOperation);

        $pregMOutput = [];
        preg_match('/[<>]=?/i', $formula, $pregMOutput);
        $this->operator = $pregMOutput[0];
        if (empty($this->operator)) {
            throw WrongRequirementDefinition::custom($this->definition);
        }

        $this->targetOperation = str_replace('=', '==', $formula);
        $this->report          = sprintf(
            "[%s][Custom] :leftSide %s :rightSide %s",
            $this->unit['name'],
            $this->operator,
            $formula
        );
    }
}

