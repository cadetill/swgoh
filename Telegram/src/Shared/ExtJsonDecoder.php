<?php

namespace Im\Shared;

class ExtJsonDecoder extends \JsonMachine\JsonDecoder\ExtJsonDecoder
{
    private $listener;

    public function __construct($assoc = false, $depth = 512, $options = 0, callable $listener = null)
    {
        parent::__construct($assoc, $depth, $options);
        $this->listener = $listener;
    }

    public function decode($jsonValue)
    {
        $result = parent::decode($jsonValue);
        if (is_null($this->listener)) {
            return $result;
        }

        $listener = $this->listener;
        $listener($jsonValue, $result->getValue());

        return $result;
    }
}
