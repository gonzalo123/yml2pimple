<?php

use G\Yaml2Pimple\ContainerBuilder;
use Pimple\Container;

include_once __DIR__ . '/fixtures/App.php';
include_once __DIR__ . '/fixtures/Curl.php';
include_once __DIR__ . '/fixtures/Proxy.php';
include_once __DIR__ . '/fixtures/Factory.php';

class ContainerBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testBuilder()
    {
        $definitionApp = $this->getMockBuilder('G\Yaml2Pimple\Definition')->disableOriginalConstructor()->getMock();
        $definitionApp->expects($this->any())->method('getClass')->will($this->returnValue('App'));
        $definitionApp->expects($this->any())->method('getArguments')->will($this->returnValue(['@Proxy', '%name%']));
        $definitionApp->expects($this->any())->method('isFactory')->will($this->returnValue(false));

        $definitionProxy = $this->getMockBuilder('G\Yaml2Pimple\Definition')->disableOriginalConstructor()->getMock();
        $definitionProxy->expects($this->any())->method('getClass')->will($this->returnValue('Proxy'));
        $definitionProxy->expects($this->any())->method('getArguments')->will($this->returnValue(['@Curl']));
        $definitionProxy->expects($this->any())->method('isFactory')->will($this->returnValue(false));

        $definitionCurl = $this->getMockBuilder('G\Yaml2Pimple\Definition')->disableOriginalConstructor()->getMock();
        $definitionCurl->expects($this->any())->method('getClass')->will($this->returnValue('Curl'));
        $definitionCurl->expects($this->any())->method('getArguments')->will($this->returnValue(null));
        $definitionCurl->expects($this->any())->method('isFactory')->will($this->returnValue(false));

        $definitionFactory = $this->getMockBuilder('G\Yaml2Pimple\Definition')->disableOriginalConstructor()->getMock();
        $definitionFactory->expects($this->any())->method('getClass')->will($this->returnValue('Factory'));
        $definitionFactory->expects($this->any())->method('getArguments')->will($this->returnValue(['@Curl']));
        $definitionFactory->expects($this->any())->method('isFactory')->will($this->returnValue(true));

        $conf = [
            'parameters' => [
                'name' => 'Gonzalo'
            ],
            'services'   => [
                'App'     => $definitionApp,
                'Proxy'   => $definitionProxy,
                'Curl'    => $definitionCurl,
                'Factory' => $definitionFactory
            ]
        ];

        $container = new Container();

        $builder = new ContainerBuilder($container);
        $builder->buildFromArray($conf);

        $this->assertInstanceOf('App', $container['App']);
        $this->assertInstanceOf('Proxy', $container['Proxy']);
        $this->assertInstanceOf('Curl', $container['Curl']);

        $factory1 = $container['Factory'];
        $this->assertInstanceOf('Factory', $factory1);
        $factory2 = $container['Factory'];
        $this->assertInstanceOf('Factory', $factory2);
        $this->assertNotSame($factory1, $factory2);
        $this->assertSame($factory1->getCurl(), $factory2->getCurl());

        $this->assertEquals('Hello Gonzalo', $container['App']->hello());
    }
}
