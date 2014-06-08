<?php

class Proxy
{
    private $curl;

    public function __construct(Curl $curl)
    {
        $this->curl = $curl;
    }

    public function hello($name)
    {
        return $this->curl->doGet($name);
    }
}