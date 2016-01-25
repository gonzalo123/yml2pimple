<?php

class Factory
{
    private $curl;

    public function __construct(Curl $curl)
    {
        $this->curl = $curl;
    }

    public function getCurl()
    {
        return $this->curl;
    }
}
