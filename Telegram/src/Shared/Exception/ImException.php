<?php

namespace Im\Shared\Exception;

use Exception;

abstract class ImException extends Exception
{
    public function __construct()
    {
        parent::__construct($this->errorMessage());
    }

    abstract public function errorCode(): string;
    abstract protected function errorMessage(): string;
}
