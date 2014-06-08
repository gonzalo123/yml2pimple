<?php

use G\Yaml2Pimple\YamlFileLoader;

include_once __DIR__ . '/fixtures/App.php';
include_once __DIR__ . '/fixtures/Curl.php';
include_once __DIR__ . '/fixtures/Proxy.php';

class YamlFileLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testLoader()
    {
        $builder = $this->getMockBuilder('G\Yaml2Pimple\ContainerBuilder')->disableOriginalConstructor()->getMock();
        $builder->expects($this->any())->method('buildFromArray')->willReturnCallback(function($conf) {
            $this->assertArrayHasKey('parameters', $conf);
            $this->assertEquals('Gonzalo', $conf['parameters']['name']);

            $this->assertArrayHasKey('services', $conf);
            $this->assertArrayHasKey('App', $conf['services']);
            $this->assertArrayHasKey('Curl', $conf['services']);
            $this->assertArrayHasKey('Proxy', $conf['services']);

            $this->assertInstanceOf('G\Yaml2Pimple\Definition', $conf['services']['App']);
            $this->assertInstanceOf('G\Yaml2Pimple\Definition', $conf['services']['Curl']);
            $this->assertInstanceOf('G\Yaml2Pimple\Definition', $conf['services']['Proxy']);

            $this->assertEquals('App', $conf['services']['App']->getClass());
            $this->assertEquals(['@Proxy', '%name%'], $conf['services']['App']->getArguments());
            $this->assertEquals('Curl', $conf['services']['Curl']->getClass());
            $this->assertEquals(null, $conf['services']['Curl']->getArguments());
            $this->assertEquals('Proxy', $conf['services']['Proxy']->getClass());
            $this->assertEquals(['@Curl'], $conf['services']['Proxy']->getArguments());
        });

        $locator = $this->getMockBuilder('Symfony\Component\Config\FileLocatorInterface')->disableOriginalConstructor()->getMock();
        $locator->expects($this->any())->method('locate')->will($this->returnValue(__DIR__ . '/fixtures/services.yml'));

        $loader = new YamlFileLoader($builder, $locator);
        $loader->load('services.yml');
    }
}