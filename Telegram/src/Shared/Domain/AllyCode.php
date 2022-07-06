<?php

namespace Im\Shared\Domain;

use InvalidArgumentException;

class AllyCode
{
    private int $value;

    public function __construct(int $allyCode)
    {
        $this->value = $allyCode;
        $this->guard();
    }

    public function value(): int
    {
        return $this->value;
    }

    private function guard()
    {
        if ($this->value < 100000000 || $this->value > 999999999) {
            throw new InvalidArgumentException(
                sprintf(
                    'The value <%s> is not a valis AllyCode',
                    $this->value
                )
            );
        }
    }
}
