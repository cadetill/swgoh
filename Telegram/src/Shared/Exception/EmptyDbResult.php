<?php

namespace Im\Shared\Exception;

class EmptyDbResult extends ImException
{
    public function __construct(string $message)
    {
        $this->message = $message;
        parent::__construct();
    }

    public function errorCode(): string
    {
        return 'empty_db_result';
    }

    protected function errorMessage(): string
    {
        return $this->message;
    }
}
