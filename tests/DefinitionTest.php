<?php

use G\Yaml2Pimple\Definition;

class DefinitionTest extends \PHPUnit_Framework_TestCase
{
    public function testDefinition()
    {
        $definition = new Definition();
        $definition->setClass('Foo');
        $definition->setArguments([1, 2, 3]);

        $this->assertEquals('Foo', $definition->getClass());
        $this->assertEquals([1, 2, 3], $definition->getArguments());
    }
}