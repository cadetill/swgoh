<?php

namespace Im\Commands\Tw\Shared\Domain;

use Im\Shared\Infrastructure\StatService;
use Im\Shared\Infrastructure\UnitRepository;

class RequirementCollection
{
    /** @var Requirement[]  */
    private array  $requirements;
    private string $definitions;

    public function __construct(string $definitions, UnitRepository $aliasRepository, StatService $statService)
    {
        $this->definitions = $definitions;
        $requirementsDefinitions = explode('),', $definitions);
        $requirements = [];
        foreach ($requirementsDefinitions as $definition) {
            if (next($requirementsDefinitions)) {
                $definition .= ')';
            }
            $requirements[] = new Requirement($definition, $aliasRepository, $statService);
        }
        $this->requirements = $requirements;
    }

    public function checkPlayer(array $roster): RequirementCollectionResult
    {
        $complain = true;
        $reports = [];
        foreach ($this->requirements as $requirement) {
            $result = $requirement->checkPlayer($roster);
            $complain &= $result->complain();
            $reports[] = $result->report();
        }

        return new RequirementCollectionResult($complain, $reports);
    }

    public function unitIds(): array
    {
        $unitIds = [];
        foreach ($this->requirements as $requirement) {
            $unitIds = array_merge($unitIds, $requirement->unitIds());
        }

        return array_unique($unitIds);
    }

    public function show()
    {
        $reports = [ '<b>'.$this->definitions."</b>\n" ];
        foreach ($this->requirements as $requirement) {
            $reports[] = $requirement->show();
        }
        return $reports;
    }
}
