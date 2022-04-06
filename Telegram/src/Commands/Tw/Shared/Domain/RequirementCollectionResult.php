<?php

namespace Im\Commands\Tw\Shared\Domain;

class RequirementCollectionResult
{
    private array $results;
    private bool  $complain;

    public function __construct(bool $complain, array $results)
    {
        $this->results  = $results;
        $this->complain = $complain;
    }

    /** @return RequirementResult[] */
    public function report()
    {
        return $this->results;
    }

    public function complain()
    {
        return $this->complain;
    }
}
