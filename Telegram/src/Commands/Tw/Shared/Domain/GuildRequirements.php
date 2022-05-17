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

    public function playerReport(array $player, bool $onlyPending = false): array
    {
        $responses = [];

        foreach ($this->requirementCollections as $requirementCollection) {
            /** @var RequirementCollection $collection */
            [ $alias, $collection ] = $requirementCollection;
            $result = $collection->checkPlayer($player);
            if ($onlyPending && $result->complain()) {
                continue;
            }

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

    public function show()
    {
        $responses = [];

        foreach ($this->requirementCollections as $requirementCollection) {
            /** @var RequirementCollection $collection */
            [ $alias, $collection ] = $requirementCollection;
            $result = $collection->show();

            $header = $alias;

            $responses[] = join("\n", [ $header, ...$result ])."\n";
        }

        return $responses;
    }

    /**
     * @return RequirementCollectionResult[]
     */
    public function playerResult(array $player)
    {
        $response = [];

        foreach ($this->requirementCollections as $requirementCollection) {
            /** @var RequirementCollection $collection */
            [ $alias, $collection ] = $requirementCollection;
            $result = $collection->checkPlayer($player);
            $response[] = $result;
        }

        return $response;
    }
}
