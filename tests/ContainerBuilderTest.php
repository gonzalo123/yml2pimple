<?php

use G\Yaml2Pimple\ContainerBuilder;
use Pimple\Container;

include_once __DIR__ . '/fixtures/App.php';
include_once __DIR__ . '/fixtures/Curl.php';
include_once __DIR__ . '/fixtures/Proxy.php';

class ContainerBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testBuilder()
    {
        $definitionApp = $this->getMockBuilder('G\Yaml2Pimple\Definition')->disableOriginalConstructor()->getMock();
        $definitionApp->expects($this->any())->method('getClass')->will($this->returnValue('App'));
        $definitionApp->expects($this->any())->method('getArguments')->will($this->returnValue(['@Proxy', '%name%']));

        $definitionProxy = $this->getMockBuilder('G\Yaml2Pimple\Definition')->disableOriginalConstructor()->getMock();
        $definitionProxy->expects($this->any())->method('getClass')->will($this->returnValue('Proxy'));
        $definitionProxy->expects($this->any())->method('getArguments')->will($this->returnValue(['@Curl']));

        $definitionCurl = $this->getMockBuilder('G\Yaml2Pimple\Definition')->disableOriginalConstructor()->getMock();
        $definitionCurl->expects($this->any())->method('getClass')->will($this->returnValue('Curl'));
        $definitionCurl->expects($this->any())->method('getArguments')->will($this->returnValue(null));

        $conf = [
            'parameters' => [
                'name' => 'Gonzalo'
            ],
            'services'   => [
                'App'   => $definitionApp,
                'Proxy' => $definitionProxy,
                'Curl'  => $definitionCurl,
            ]
        ];

        $container = new Container();

        $builder = new ContainerBuilder($container);
        $builder->buildFromArray($conf);

        $this->assertInstanceOf('App', $container['App']);
        $this->assertInstanceOf('Proxy', $container['Proxy']);
        $this->assertInstanceOf('Curl', $container['Curl']);

        $this->assertEquals('Hello Gonzalo', $container['App']->hello());
    }
}