<?php

namespace G\Yaml2Pimple;

class Definition
{
    private $class;

    protected $arguments;

    protected $factory = false;

    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    public function getClass()
    {
        return $this->class;
    }

    public function setArguments(array $arguments)
    {
        $this->arguments = $arguments;

        return $this;
    }

    public function getArguments()
    {
        return $this->arguments;
    }

    public function setFactory($factory)
    {
        $this->factory = (bool) $factory;

        return $this;
    }

    public function isFactory()
    {
        return $this->factory;
    }
}
