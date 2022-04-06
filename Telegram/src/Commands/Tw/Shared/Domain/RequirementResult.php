<?php

namespace Im\Commands\Tw\Shared\Domain;

class RequirementResult
{
    private bool   $complain;
    private string $report;

    public function __construct(bool $complain, string $report)
    {
        $this->complain = $complain;
        $this->report   = $report;
    }

    public function complain(): bool
    {
        return $this->complain;
    }

    public function report()
    {
        return sprintf(
            '%s %s',
            $this->complain ? "\xF0\x9F\x94\xB9" : "\xF0\x9F\x94\xBB",
            $this->report
        );
    }
}
