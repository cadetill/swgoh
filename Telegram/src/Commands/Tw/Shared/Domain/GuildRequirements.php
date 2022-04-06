<?php

namespace Im\Commands\Tw\Shared\Domain;

class GuildRequirements
{
    private array $requirementCollections;

    public function __construct(array ...$requirementCollections)
    {
        $this->requirementCollections = $requirementCollections;
    }

    public function unitIds()
    {
        $unitIds = [];
        foreach ($this->requirementCollections as $requirementCollection) {
            /** @var RequirementCollection $collection */
            [ $alias, $collection ] = $requirementCollection;
            $unitIds = array_merge($unitIds, $collection->unitIds());
        }

        return array_unique($unitIds);
    }

    public function checkPlayer(array $player): array
    {
        $responses = [];

        foreach ($this->requirementCollections as $requirementCollection) {
            /** @var RequirementCollection $collection */
            [ $alias, $collection ] = $requirementCollection;
            $result = $collection->checkPlayer($player);

            $header = sprintf(
                "<b>%s %s</b>",
                $result->complain() ? "\xF0\x9F\x94\xB5" : "\xF0\x9F\x94\xB4",
                $alias
            );

            $content = $result->report();

            $responses[] = join("\n", [ $header, ...$content ]);
        }

        return $responses;
    }
}
